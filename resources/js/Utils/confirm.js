// resources/js/Utils/confirm.js
import { confirm as inertiaConfirm } from '@inertiajs/vue3'

export function confirm(message, options = {}) {
    const defaultOptions = {
        title: 'Confirmar acción',
        message: message,
        style: 'danger',
        confirmText: 'Sí, continuar',
        cancelText: 'Cancelar',
        ...options
    }

    return new Promise((resolve) => {
        inertiaConfirm(defaultOptions, resolve)
    })
}

// Función adicional para confirmaciones de eliminación
export function confirmDelete(itemName = 'este elemento') {
    return confirm(
        `¿Está seguro de que desea eliminar ${itemName}? Esta acción no se puede deshacer.`,
        {
            title: 'Confirmar eliminación',
            style: 'danger',
            confirmText: 'Sí, eliminar'
        }
    )
}

// Función para confirmaciones de cancelación
export function confirmCancel(itemName = 'este elemento') {
    return confirm(
        `¿Está seguro de que desea cancelar ${itemName}? Esta acción no se puede deshacer.`,
        {
            title: 'Confirmar cancelación',
            style: 'warning',
            confirmText: 'Sí, cancelar'
        }
    )
}
