<script setup>
import { ref, onMounted, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { EyeIcon } from '@heroicons/vue/24/outline'

const currentPageViews = ref(0)
const currentPageName = ref('')
const loading = ref(true)
const page = usePage()

// Helper para generar rutas con la base URL correcta
const route = (name, params = {}) => {
  const routes = window.routes || {}
  let url = routes[name] || `/${name}`
  
  // Reemplazar parámetros en la URL
  Object.keys(params).forEach(key => {
    url = url.replace(`{${key}}`, params[key])
  })
  
  return url
}

const fetchPageViews = async () => {
  try {
    // Esperar un momento a que el middleware registre la vista
    await new Promise(resolve => setTimeout(resolve, 500))
    
    const pageName = extractPageName()
    
    if (!pageName) {
      currentPageViews.value = 0
      loading.value = false
      return
    }

    currentPageName.value = pageName

    // Obtener las vistas de esta página específica
    // Usar route helper de Inertia para construir la URL correctamente
    const url = route('api.page-views.page', { pageName: encodeURIComponent(pageName) })
    console.log('📊 Fetching page views:', { pageName, url })
    
    const response = await fetch(url)
    
    if (!response.ok) {
      console.error('Error response:', response.status, response.statusText)
      loading.value = false
      return
    }
    
    const data = await response.json()
    console.log(`Page: ${pageName}, Views:`, data)
    currentPageViews.value = parseInt(data) || 0
  } catch (error) {
    console.error('Error fetching page views:', error)
    currentPageViews.value = 0
  } finally {
    loading.value = false
  }
}

const extractPageName = () => {
  const url = page.url || ''
  const component = page.component || ''
  
  console.log('Current URL:', url)
  console.log('Current Component:', component)
  
  // Detectar si es create, edit o show basado en la URL
  const isCreate = url.includes('/create')
  const isEdit = url.includes('/edit')
  const isShow = /\/\d+$/.test(url) && !url.includes('/edit') // Solo números al final
  
  // Admin routes - con detección de sub-acciones
  if (component.includes('Admin/Vehiculo')) {
    if (isCreate) return 'Crear Vehículo'
    if (isEdit) return 'Editar Vehículo'
    if (isShow) return 'Ver Vehículo'
    return 'Gestión de Vehículos'
  } else if (component.includes('Admin/Cita')) {
    if (isCreate) return 'Crear Cita'
    if (isEdit) return 'Editar Cita'
    if (isShow) return 'Ver Cita'
    return 'Gestión de Citas'
  } else if (component.includes('Admin/Diagnostico')) {
    if (isCreate) return 'Crear Diagnóstico'
    if (isEdit) return 'Editar Diagnóstico'
    if (isShow) return 'Ver Diagnóstico'
    return 'Diagnósticos'
  } else if (component.includes('Admin/Orden')) {
    if (isCreate) return 'Crear Orden'
    if (isEdit) return 'Editar Orden'
    if (isShow) return 'Ver Orden'
    return 'Órdenes de Trabajo'
  } else if (component.includes('Admin/Pago')) {
    if (isCreate) return 'Crear Pago'
    if (isEdit) return 'Editar Pago'
    if (isShow) return 'Ver Pago'
    return 'Pagos'
  } else if (component.includes('Admin/Reporte')) {
    return 'Reportes'
  } else if (component.includes('Admin/Cliente')) {
    if (isCreate) return 'Crear Cliente'
    if (isEdit) return 'Editar Cliente'
    if (isShow) return 'Ver Cliente'
    return 'Gestión de Clientes'
  } else if (component.includes('Admin/Servicio')) {
    if (isCreate) return 'Crear Servicio'
    if (isEdit) return 'Editar Servicio'
    if (isShow) return 'Ver Servicio'
    return 'Servicios'
  } else if (component.includes('Dashboard')) {
    return 'Dashboard'
  }
  // Cliente routes
  else if (component.includes('Cliente/Vehiculo')) {
    if (isCreate) return 'Crear Vehículo'
    if (isEdit) return 'Editar Vehículo'
    if (isShow) return 'Ver Vehículo'
    return 'Mis Vehículos'
  } else if (component.includes('Cliente/Cita')) {
    if (isCreate) return 'Crear Cita'
    if (isEdit) return 'Editar Cita'
    if (isShow) return 'Ver Cita'
    return 'Mis Citas'
  } else if (component.includes('Cliente/Orden')) {
    if (isShow) return 'Ver Orden'
    return 'Mis Órdenes'
  } else if (component.includes('Cliente/Pago')) {
    if (isShow) return 'Ver Pago'
    return 'Mis Pagos'
  }
  // Mecanico routes
  else if (component.includes('Mecanico/Diagnostico')) {
    if (isCreate) return 'Crear Diagnóstico'
    if (isEdit) return 'Editar Diagnóstico'
    if (isShow) return 'Ver Diagnóstico'
    return 'Diagnósticos'
  } else if (component.includes('Mecanico/Orden')) {
    if (isEdit) return 'Editar Orden'
    if (isShow) return 'Ver Orden'
    return 'Órdenes de Trabajo'
  }
  // Profile
  else if (component.includes('Profile')) {
    return 'Editar Perfil'
  }
  
  // Fallback: detectar por URL si el componente no fue específico
  if (url.includes('/dashboard')) {
    return 'Dashboard'
  } else if (url.includes('/admin/vehiculos')) {
    if (isCreate) return 'Crear Vehículo'
    if (isEdit) return 'Editar Vehículo'
    if (isShow) return 'Ver Vehículo'
    return 'Gestión de Vehículos'
  } else if (url.includes('/admin/citas')) {
    if (isCreate) return 'Crear Cita'
    if (isEdit) return 'Editar Cita'
    if (isShow) return 'Ver Cita'
    return 'Gestión de Citas'
  } else if (url.includes('/admin/diagnosticos')) {
    if (isCreate) return 'Crear Diagnóstico'
    if (isEdit) return 'Editar Diagnóstico'
    if (isShow) return 'Ver Diagnóstico'
    return 'Diagnósticos'
  } else if (url.includes('/orden-trabajos')) {
    if (isCreate) return 'Crear Orden'
    if (isEdit) return 'Editar Orden'
    if (isShow) return 'Ver Orden'
    return 'Órdenes de Trabajo'
  } else if (url.includes('/admin/ordenes')) {
    if (isCreate) return 'Crear Orden'
    if (isEdit) return 'Editar Orden'
    if (isShow) return 'Ver Orden'
    return 'Órdenes de Trabajo'
  } else if (url.includes('/admin/pagos')) {
    if (isCreate) return 'Crear Pago'
    if (isEdit) return 'Editar Pago'
    if (isShow) return 'Ver Pago'
    return 'Pagos'
  } else if (url.includes('/admin/reportes')) {
    return 'Reportes'
  } else if (url.includes('/clientes')) {
    if (isCreate) return 'Crear Cliente'
    if (isEdit) return 'Editar Cliente'
    if (isShow) return 'Ver Cliente'
    return 'Gestión de Clientes'
  } else if (url.includes('/servicios')) {
    if (isCreate) return 'Crear Servicio'
    if (isEdit) return 'Editar Servicio'
    if (isShow) return 'Ver Servicio'
    return 'Gestión de Servicios'
  } else if (url.includes('/usuarios')) {
    if (isCreate) return 'Crear Usuario'
    if (isEdit) return 'Editar Usuario'
    if (isShow) return 'Ver Usuario'
    return 'Gestión de Usuarios'
  } else if (url.includes('/marcas')) {
    if (isCreate) return 'Crear Marca'
    if (isEdit) return 'Editar Marca'
    if (isShow) return 'Ver Marca'
    return 'Gestión de Marcas'
  } else if (url.includes('/modelos')) {
    if (isCreate) return 'Crear Modelo'
    if (isEdit) return 'Editar Modelo'
    if (isShow) return 'Ver Modelo'
    return 'Gestión de Modelos'
  } else if (url.includes('/motores')) {
    if (isCreate) return 'Crear Motor'
    if (isEdit) return 'Editar Motor'
    if (isShow) return 'Ver Motor'
    return 'Gestión de Motores'
  } else if (url.includes('/partes')) {
    if (isCreate) return 'Crear Parte'
    if (isEdit) return 'Editar Parte'
    if (isShow) return 'Ver Parte'
    return 'Gestión de Partes'
  } else if (url.includes('/incidencias')) {
    if (isCreate) return 'Crear Incidencia'
    if (isEdit) return 'Editar Incidencia'
    if (isShow) return 'Ver Incidencia'
    return 'Gestión de Incidencias'
  } else if (url.includes('/plan-pagos')) {
    if (isCreate) return 'Crear Plan de Pago'
    if (isEdit) return 'Editar Plan de Pago'
    if (isShow) return 'Ver Plan de Pago'
    return 'Planes de Pago'
  } 
  // Cliente routes
  else if (url.includes('/mis-vehiculos') || url.includes('/cliente/vehiculos')) {
    if (isCreate) return 'Crear Vehículo'
    if (isEdit) return 'Editar Vehículo'
    if (isShow) return 'Ver Vehículo'
    return 'Mis Vehículos'
  } else if (url.includes('/mis-citas') || url.includes('/cliente/citas')) {
    if (isCreate) return 'Crear Cita'
    if (isEdit) return 'Editar Cita'
    if (isShow) return 'Ver Cita'
    return 'Mis Citas'
  } else if (url.includes('/ordenes')) {
    if (isShow) return 'Ver Orden'
    return 'Mis Órdenes'
  } else if (url.includes('/mis-pagos') || url.includes('/cliente/pagos')) {
    if (isShow) return 'Ver Pago'
    return 'Mis Pagos'
  } 
  // Mecanico routes
  else if (url.includes('/mecanico/diagnosticos')) {
    if (isCreate) return 'Crear Diagnóstico'
    if (isEdit) return 'Editar Diagnóstico'
    if (isShow) return 'Ver Diagnóstico'
    return 'Diagnósticos'
  } 
  // Profile
  else if (url.includes('/profile')) {
    return 'Editar Perfil'
  } else if (url.includes('/usuarios')) {
    if (isCreate) return 'Crear Usuario'
    if (isEdit) return 'Editar Usuario'
    if (isShow) return 'Ver Usuario'
    return 'Gestión de Usuarios'
  }
  
  console.log('Component name not mapped:', component)
  return null
}

onMounted(async () => {
  await fetchPageViews()
})

// Observar cambios de página
watch(() => page.url, async () => {
  loading.value = true
  currentPageViews.value = 0
  await fetchPageViews()
})
</script>

<template>
  <div v-if="!loading" class="fixed bottom-6 right-6 z-40">
    <!-- Contenedor con efecto glassmorphism -->
    <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md rounded-full shadow-lg border border-white/20 px-6 py-3 flex items-center gap-3 hover:shadow-xl transition-all duration-300"
      :style="{ 
        backgroundColor: 'rgba(var(--color-base-rgb), 0.8)',
        borderColor: 'var(--color-border)',
      }"
    >
      <!-- Icono del ojo con animación -->
      <div class="relative">
        <EyeIcon class="w-5 h-5 transition-transform duration-300"
          :style="{ color: 'var(--color-primary)' }"
        />
        <div class="absolute inset-0 rounded-full opacity-0 hover:opacity-20 transition-opacity"
          :style="{ backgroundColor: 'var(--color-primary)' }"
        />
      </div>

      <!-- Contador de visitas de esta página -->
      <div class="flex flex-col items-start">
        <span class="text-xs font-medium uppercase tracking-wide"
          :style="{ color: 'var(--color-text-light)' }"
        >
          Visitas
        </span>
        <span class="text-sm font-bold"
          :style="{ color: 'var(--color-text)' }"
        >
          {{ currentPageViews }}
        </span>
      </div>

      <!-- Tooltip -->
      <div class="ml-2 group relative">
        <div class="w-1.5 h-1.5 rounded-full"
          :style="{ backgroundColor: 'var(--color-primary)' }"
        />
        <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap"
        >
          Visitas en esta página
          <div class="absolute top-full right-2 w-2 h-2 bg-gray-800 transform rotate-45" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Animación de pulso suave para el indicador */
@keyframes pulse-soft {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse-soft {
  animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
