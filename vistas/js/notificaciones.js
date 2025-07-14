$(function () {
    // ===============================
    // INICIALIZACIÓN
    // ===============================
    
    initializeCheckboxToggle();
    initializeStarToggle();
    initializeTextEditor();
    initializeNotificationSystem();

    // ===============================
    // FUNCIONES DE INICIALIZACIÓN
    // ===============================
    
    function initializeCheckboxToggle() {
        $('.checkbox-toggle').click(function () {
            const clicks = $(this).data('clicks');
            const checkboxes = $('.mailbox-messages input[type="checkbox"]');
            const icon = $('.checkbox-toggle .far');
            
            if (clicks) {
                checkboxes.prop('checked', false);
                icon.removeClass('fa-check-square').addClass('fa-square');
            } else {
                checkboxes.prop('checked', true);
                icon.removeClass('fa-square').addClass('fa-check-square');
            }
            $(this).data('clicks', !clicks);
        });
    }

    function initializeStarToggle() {
        $('.mailbox-star').click(function (e) {
            e.preventDefault();
            const star = $(this).find('a > i');
            if (star.hasClass('fa')) {
                star.toggleClass('fa-star fa-star-o');
            }
        });
    }

    function initializeTextEditor() {
        $('#compose-textarea').summernote();
    }

    function initializeNotificationSystem() {
        updateNotificationCounters();
        loadNotificationDropdown();
        startAutoUpdates();
        bindNotificationEvents();
    }

    // ===============================
    // SISTEMA DE NOTIFICACIONES
    // ===============================
    
    function updateNotificationCounters() {
        makeAjaxRequest('contadores', {}, function(response) {
            if (response.success && response.totalNotificaciones !== undefined) {
                $('#count-notificaciones').text(response.totalNotificaciones);
            }
        });
    }

    function updateUnreadNotifications() {
        makeAjaxRequest('no_leidas', {}, function(response) {
            if (response.success && response.noLeidas !== undefined) {
                $('#count-no-leidas').text(response.noLeidas);
                updateBellCounter(response.noLeidas);
            }
        });
    }

    function loadNotificationDropdown() {
        makeAjaxRequest('dropdown_notificaciones', {}, function(response) {
            if (response.success && response.notificaciones) {
                updateDropdownContent(response.notificaciones);
            }
        });
    }

    function updateDropdownContent(notifications) {
        const unreadNotifications = notifications.filter(n => n.leida == 0);
        let content = '';

        if (unreadNotifications.length > 0) {
            content = buildNotificationItems(unreadNotifications.slice(0, 3));
        } else {
            content = '<div class="dropdown-item text-center text-muted">No hay notificaciones</div>';
        }

        content += buildViewAllLink();
        updateDropdownElements(content);
    }

    function buildNotificationItems(notifications) {
        return notifications.map((notif, index) => {
            const formattedDate = formatDate(notif.fecha_creacion);
            const divider = index < notifications.length - 1 ? '<div class="dropdown-divider"></div>' : '';
            
            return `
                <a href="${notif.url || '#'}" class="dropdown-item notification-unread font-weight-bold" data-id="${notif.id_notificaciones}">
                    <div class="media">
                        <div class="media-object">
                            <i class="fas fa-envelope text-primary"></i>
                        </div>
                        <div class="media-body ml-2">
                            <h6 class="media-heading text-sm">Evento #${notif.id_tipo_evento}</h6>
                            <p class="text-sm text-muted mb-0">${notif.mensaje}</p>
                            <p class="text-xs text-muted mb-0">
                                <i class="far fa-clock mr-1"></i>${formattedDate}
                            </p>
                        </div>
                    </div>
                </a>${divider}
            `;
        }).join('');
    }

    function buildViewAllLink() {
        return `
            <div class="dropdown-divider"></div>
            <a href="notificaciones" class="dropdown-item dropdown-footer text-center" id="ver-todas-notificaciones">
                <i class="fas fa-envelope mr-2"></i>Ver todas las notificaciones
            </a>
        `;
    }

    function updateDropdownElements(content) {
        const selectors = [
            '.dropdown-menu-notificaciones',
            '#dropdown-notificaciones .dropdown-menu',
            '.notification-dropdown .dropdown-menu',
            '#campana-notificaciones + .dropdown-menu'
        ];

        const updated = selectors.some(selector => {
            const element = $(selector);
            if (element.length > 0) {
                element.html(content);
                return true;
            }
            return false;
        });

        if (!updated) {
            console.warn('Dropdown element not found');
        }
    }

    function updateBellCounter(unreadCount) {
        const badge = $('#campana-notificaciones .badge, .notification-bell .badge, #notification-count');
        const bell = $('#campana-notificaciones, .notification-bell');

        if (unreadCount > 0) {
            badge.text(unreadCount).show().removeClass('d-none badge-danger').addClass('badge-warning');
            bell.addClass('has-notifications');
        } else {
            badge.hide().addClass('d-none').removeClass('badge-danger badge-warning');
            bell.removeClass('has-notifications');
        }
    }

    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'Hace un momento';
            if (minutes < 60) return `Hace ${minutes} min`;
            if (hours < 24) return `Hace ${hours}h`;
            if (days < 7) return `Hace ${days}d`;
            
            return date.toLocaleDateString();
        } catch (e) {
            return dateString;
        }
    }

    function startAutoUpdates() {
        // Actualización inicial
        updateUnreadNotifications();
        updateNotificationCounters();
        loadNotificationDropdown();

        // Actualización cada 3 segundos
        setInterval(() => {
            updateUnreadNotifications();
            updateNotificationCounters();
            loadNotificationDropdown();
        }, 3000);

        // Actualizar al hacer foco en la ventana
        $(window).focus(() => {
            updateUnreadNotifications();
            updateNotificationCounters();
            loadNotificationDropdown();
        });
    }

    // ===============================
    // EVENTOS Y ACCIONES
    // ===============================
    
    function bindNotificationEvents() {
        // Dropdown abierto
        $(document).on('show.bs.dropdown', function(e) {
            if (isNotificationDropdown(e.target)) {
                loadNotificationDropdown();
            }
        });

        // Marcar como leída desde dropdown
        $(document).on('click', '.dropdown-item[data-id]', function(e) {
            const id = $(this).data('id');
            if (id && $(this).hasClass('notification-unread')) {
                markAsRead(id);
            }
        });

        // Ver todas las notificaciones
        $(document).on('click', '#ver-todas-notificaciones', function(e) {
            if (isFromDropdown($(this))) {
                markAllAsRead();
            }
        });

        // Eliminar notificaciones
        $(document).on('click', '.btn-delete', function () {
            const id = $(this).data('id');
            deleteNotification(id);
        });

        $(document).on('click', '.btn-delete-selected', function () {
            deleteSelectedNotifications();
        });

        // Cargar leídos
        $('#btn-leidos').on('click', function(e) {
            e.preventDefault();
            loadReadNotifications();
        });
    }

    function isNotificationDropdown(target) {
        return $(target).find('#campana-notificaciones').length > 0 || 
               $(target).hasClass('notification-dropdown') ||
               $(target).attr('id') === 'campana-notificaciones';
    }

    function isFromDropdown(element) {
        return element.attr('id') === 'ver-todas-notificaciones' || 
               element.closest('.dropdown-menu').length > 0;
    }

    function markAsRead(id) {
        makeAjaxRequest('marcar_leida', { id }, function(response) {
            if (response.success) {
                refreshNotifications();
            }
        });
    }

    function markAllAsRead() {
        makeAjaxRequest('marcar_todas_leidas', {}, function(response) {
            if (response.success) {
                updateBellCounter(0);
                refreshNotifications();
            }
        });
    }

    function deleteNotification(id) {
        makeAjaxRequest('', { id }, function(response) {
            if (response.success) {
                $('#notificacion-' + id).remove();
                showSuccessMessage('Notificación eliminada');
                refreshNotifications();
            } else {
                showErrorMessage('Error al eliminar: ' + response.message);
            }
        });
    }

    function deleteSelectedNotifications() {
        const ids = getSelectedIds();
        
        if (ids.length === 0) {
            showInfoMessage('No hay notificaciones seleccionadas');
            return;
        }

        makeAjaxRequest('', { ids }, function(response) {
            if (response.success) {
                ids.forEach(id => $('#notificacion-' + id).remove());
                showSuccessMessage('Notificaciones eliminadas');
                refreshNotifications();
            } else {
                showErrorMessage(response.message || 'Error al eliminar');
            }
        });
    }

    function loadReadNotifications() {
        if ($('#contenido-notificaciones').length === 0) {
            $('body').append('<div id="contenido-notificaciones"></div>');
        }
        
        $('#contenido-notificaciones').html('<div class="text-center">Cargando...</div>');
        
        $.ajax({
            url: 'vistas/modulos/leidos.php',
            type: 'GET',
            success: data => $('#contenido-notificaciones').html(data),
            error: () => $('#contenido-notificaciones').html('<div class="text-danger">Error al cargar.</div>')
        });
    }

    // ===============================
    // FUNCIONES AUXILIARES
    // ===============================
    
    function makeAjaxRequest(action, data, successCallback) {
        const requestData = action ? { accion: action, ...data } : data;
        
        $.ajax({
            url: 'ajax/notificaciones.ajax.php',
            type: 'POST',
            dataType: 'json',
            data: requestData,
            success: successCallback,
            error: () => console.log('Error en petición AJAX')
        });
    }

    function refreshNotifications() {
        updateUnreadNotifications();
        updateNotificationCounters();
        loadNotificationDropdown();
    }

    function getSelectedIds() {
        const ids = [];
        $('.mailbox-messages input[type="checkbox"]:checked').each(function () {
            const id = $(this).val();
            if (id) ids.push(id);
        });
        return ids;
    }

    function showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
    }

    function showErrorMessage(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message
        });
    }

    function showInfoMessage(message) {
        Swal.fire({
            icon: 'info',
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
    }
});