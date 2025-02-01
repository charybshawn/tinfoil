import { usePage } from '@inertiajs/vue3'

export function useRoles() {
    const hasRole = (role) => {
        return usePage().props.auth.user?.roles.includes(role)
    }

    return {
        hasRole
    }
} 