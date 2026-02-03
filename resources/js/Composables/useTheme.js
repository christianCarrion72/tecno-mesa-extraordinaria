import { ref, watch, onMounted } from 'vue'

// Lista de temas disponibles con colores armoniosos
const availableThemes = [
    {
        value: 'claro',
        name: '☀️ Claro',
        icon: '☀️',
        colors: {
            primary: '#3b82f6',     // blue-500 - azul brillante
            secondary: '#8b5cf6',   // violet-500 - violeta
            accent: '#06b6d4',      // cyan-500 - cian
            neutral: '#6b7280',     // gray-500 - gris medio
            base: '#ffffff',        // white - blanco puro
            info: '#0ea5e9',        // sky-500 - azul cielo
            success: '#10b981',     // emerald-500 - verde esmeralda
            warning: '#f59e0b',     // amber-500 - ámbar
            error: '#ef4444',       // red-500 - rojo
            sidebarBg: '#f8fafc',   // slate-50 - gris muy claro
            text: '#1f2937',        // gray-800 - texto oscuro
            textLight: '#6b7280',   // gray-500 - texto claro
            border: '#e5e7eb',      // gray-200 - borde
        }
    },
    {
        value: 'oscuro',
        name: '🌙 Oscuro',
        icon: '🌙',
        colors: {
            primary: '#60a5fa',     // blue-400 - azul claro
            secondary: '#a78bfa',   // violet-400 - violeta claro
            accent: '#22d3ee',      // cyan-400 - cian claro
            neutral: '#9ca3af',     // gray-400 - gris claro
            base: '#111827',        // gray-900 - gris muy oscuro
            info: '#38bdf8',        // sky-400 - cielo claro
            success: '#34d399',     // emerald-400 - esmeralda claro
            warning: '#fbbf24',     // amber-400 - ámbar claro
            error: '#f87171',       // red-400 - rojo claro
            sidebarBg: '#1f2937',   // gray-800 - gris oscuro
            text: '#f9fafb',        // gray-50 - texto claro
            textLight: '#d1d5db',   // gray-300 - texto medio
            border: '#374151',      // gray-700 - borde oscuro
        }
    },
    {
        value: 'jovenes',
        name: '🎨 Jóvenes',
        icon: '🎨',
        colors: {
            primary: '#ec4899',     // pink-500 - rosa vibrante
            secondary: '#8b5cf6',   // violet-500 - violeta
            accent: '#06b6d4',      // cyan-500 - cian
            neutral: '#f97316',     // orange-500 - naranja
            base: '#fef7ed',        // orange-50 - fondo cálido
            info: '#3b82f6',        // blue-500 - azul
            success: '#10b981',     // emerald-500 - verde
            warning: '#f59e0b',     // amber-500 - amarillo
            error: '#ef4444',       // red-500 - rojo
            sidebarBg: '#fed7aa',   // orange-200 - sidebar cálido
            text: '#9a3412',        // orange-800 - texto oscuro
            textLight: '#ea580c',   // orange-600 - texto medio
            border: '#fed7aa',      // orange-200 - borde cálido
        }
    },
    {
        value: 'adultos',
        name: '💼 Adultos',
        icon: '💼',
        colors: {
            primary: '#1e40af',     // blue-800 - azul profesional
            secondary: '#64748b',   // slate-500 - gris azulado
            accent: '#0891b2',      // cyan-600 - cian profesional
            neutral: '#475569',     // slate-600 - gris oscuro
            base: '#f8fafc',        // slate-50 - fondo muy claro
            info: '#0284c7',        // sky-600 - azul cielo
            success: '#15803d',     // green-700 - verde oscuro
            warning: '#b45309',     // amber-700 - ámbar oscuro
            error: '#7f1d1d',       // red-900 - rojo oscuro
            sidebarBg: '#e2e8f0',   // slate-200 - sidebar gris
            text: '#0f172a',        // slate-900 - texto muy oscuro
            textLight: '#64748b',   // slate-500 - texto gris
            border: '#cbd5e1',      // slate-300 - borde gris
        }
    },
    {
        value: 'ninos',
        name: '🎪 Niños',
        icon: '🎪',
        colors: {
            primary: '#f59e0b',     // amber-500 - amarillo soleado
            secondary: '#ec4899',   // pink-500 - rosa
            accent: '#8b5cf6',      // violet-500 - morado
            neutral: '#06b6d4',     // cyan-500 - azul cielo
            base: '#fefce8',        // yellow-50 - fondo amarillo claro
            info: '#3b82f6',        // blue-500 - azul
            success: '#10b981',     // emerald-500 - verde
            warning: '#f97316',     // orange-500 - naranja
            error: '#ef4444',       // red-500 - rojo
            sidebarBg: '#fef3c7',   // yellow-100 - sidebar amarillo
            text: '#92400e',        // amber-800 - texto marrón
            textLight: '#d97706',   // amber-600 - texto naranja
            border: '#fde68a',      // yellow-200 - borde amarillo
        }
    },
]

// Estado global del tema
const theme = ref('light')
const isInitialized = ref(false)

// Función para obtener el tema guardado en localStorage
const getStoredTheme = () => {
    if (typeof window !== 'undefined') {
        try {
            const stored = localStorage.getItem('theme') || 'light'
            // Verificar que el tema guardado esté en la lista de disponibles
            const isValidTheme = availableThemes.some(t => t.value === stored)
            return isValidTheme ? stored : 'light'
        } catch (error) {
            console.warn('Error al leer el tema desde localStorage:', error)
            return 'light'
        }
    }
    return 'light'
}

// Función para aplicar el tema al DOM y guardarlo en localStorage
const applyTheme = (newTheme) => {
    if (typeof document !== 'undefined') {
        const themeConfig = availableThemes.find(t => t.value === newTheme)
        if (!themeConfig) return
        
        try {
            // Aplicar variables CSS personalizadas al root
            const root = document.documentElement
            
            Object.entries(themeConfig.colors).forEach(([key, value]) => {
                root.style.setProperty(`--color-${key}`, value)
            })
            
            // Aplicar clase dark para Tailwind si es un tema oscuro
            const darkThemes = ['dark', 'taller-night', 'neon']
            if (darkThemes.includes(newTheme)) {
                root.classList.add('dark')
            } else {
                root.classList.remove('dark')
            }
            
            // Aplicar clase del tema
            root.setAttribute('data-theme', newTheme)
            
            // Guardar en localStorage
            localStorage.setItem('theme', newTheme)
            theme.value = newTheme
            
            console.log('Tema aplicado:', newTheme)
            console.log('Colores:', themeConfig.colors)
        } catch (error) {
            console.error('Error al aplicar el tema:', error)
        }
    }
}

// Inicializar el tema solo una vez
const initializeTheme = () => {
    if (!isInitialized.value && typeof window !== 'undefined') {
        const storedTheme = getStoredTheme()
        theme.value = storedTheme
        applyTheme(storedTheme)
        isInitialized.value = true
        console.log('Tema inicializado:', storedTheme)
    }
}

// Observar cambios en el tema
watch(theme, (newTheme) => {
    if (isInitialized.value) {
        applyTheme(newTheme)
    }
})

export function useTheme() {
    // Inicializar en el primer uso
    onMounted(() => {
        initializeTheme()
    })
    
    // Si ya estamos en el navegador, inicializar inmediatamente
    if (typeof window !== 'undefined' && !isInitialized.value) {
        initializeTheme()
    }

    const toggleTheme = () => {
        const currentIndex = availableThemes.findIndex(t => t.value === theme.value)
        const nextIndex = (currentIndex + 1) % availableThemes.length
        const newTheme = availableThemes[nextIndex].value
        theme.value = newTheme
        console.log('Toggleando tema a:', newTheme)
    }

    const setTheme = (newTheme) => {
        const isValidTheme = availableThemes.some(t => t.value === newTheme)
        if (isValidTheme) {
            theme.value = newTheme
        }
    }

    const getCurrentThemeInfo = () => {
        return availableThemes.find(t => t.value === theme.value) || availableThemes[0]
    }

    const getThemeColors = () => {
        const currentTheme = getCurrentThemeInfo()
        return currentTheme.colors
    }

    return {
        theme,
        availableThemes,
        toggleTheme,
        setTheme,
        getCurrentThemeInfo,
        getThemeColors,
        isInitialized
    }
}
