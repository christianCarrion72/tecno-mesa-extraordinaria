import { useTheme } from '@/Composables/useTheme'

export default {
    install(app) {
        // Inicializar el tema globalmente de inmediato
        if (typeof window !== 'undefined') {
            // Obtener tema guardado o usar por defecto
            try {
                const stored = localStorage.getItem('theme') || 'light'
                const root = document.documentElement
                
                // Aplicar el tema inmediatamente
                const availableThemes = [
                    { 
                        value: 'light', 
                        colors: {
                            primary: '#2563eb', secondary: '#7c3aed', accent: '#0891b2',
                            neutral: '#374151', base: '#ffffff', info: '#0284c7',
                            success: '#16a34a', warning: '#d97706', error: '#dc2626',
                            sidebarBg: '#f3f4f6',
                        }
                    },
                    { 
                        value: 'dark', 
                        colors: {
                            primary: '#60a5fa', secondary: '#a78bfa', accent: '#22d3ee',
                            neutral: '#e5e7eb', base: '#111827', info: '#38bdf8',
                            success: '#4ade80', warning: '#fbbf24', error: '#f87171',
                            sidebarBg: '#1f2937',
                        }
                    },
                    { 
                        value: 'taller-day', 
                        colors: {
                            primary: '#dc2626', secondary: '#ea580c', accent: '#0891b2',
                            neutral: '#374151', base: '#fef3c7', info: '#0284c7',
                            success: '#16a34a', warning: '#d97706', error: '#991b1b',
                            sidebarBg: '#fed7aa',
                        }
                    },
                    { 
                        value: 'taller-night', 
                        colors: {
                            primary: '#ef4444', secondary: '#f97316', accent: '#06b6d4',
                            neutral: '#e5e7eb', base: '#1f2937', info: '#38bdf8',
                            success: '#4ade80', warning: '#fbbf24', error: '#f87171',
                            sidebarBg: '#111827',
                        }
                    },
                    { 
                        value: 'professional', 
                        colors: {
                            primary: '#1e40af', secondary: '#64748b', accent: '#0891b2',
                            neutral: '#475569', base: '#f8fafc', info: '#0284c7',
                            success: '#15803d', warning: '#b45309', error: '#7f1d1d',
                            sidebarBg: '#e2e8f0',
                        }
                    },
                    { 
                        value: 'neon', 
                        colors: {
                            primary: '#ff006e', secondary: '#8338ec', accent: '#3a86ff',
                            neutral: '#ffbe0b', base: '#0a0e27', info: '#00f5ff',
                            success: '#00d084', warning: '#ffbe0b', error: '#ff006e',
                            sidebarBg: '#1a1a2e',
                        }
                    },
                ]
                
                const themeConfig = availableThemes.find(t => t.value === stored)
                if (themeConfig) {
                    // Aplicar variables CSS
                    Object.entries(themeConfig.colors).forEach(([key, value]) => {
                        root.style.setProperty(`--color-${key}`, value)
                    })
                    
                    // Aplicar clases
                    const darkThemes = ['dark', 'taller-night', 'neon']
                    if (darkThemes.includes(stored)) {
                        root.classList.add('dark')
                    } else {
                        root.classList.remove('dark')
                    }
                    
                    root.setAttribute('data-theme', stored)
                    console.log('🎨 Tema aplicado al iniciar:', stored)
                }
            } catch (e) {
                console.error('Error al inicializar tema:', e)
            }
        }
        
        // Hacer el composable disponible en todos los componentes
        app.provide('useTheme', useTheme)
        console.log('✅ Plugin de temas inicializado')
    }
}

