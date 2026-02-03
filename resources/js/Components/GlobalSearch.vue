<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    default: 'Buscar... (Ctrl+F)'
  },
  autofocus: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'search'])

const inputRef = ref(null)
const localValue = ref(props.modelValue || '')
const currentMatchIndex = ref(0)
const totalMatches = ref(0)

function handleKeydown(e) {
  // Ctrl+F o Cmd+F (Mac)
  if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
    e.preventDefault()
    searchMode.value = 'page'
    inputRef.value?.focus()
    inputRef.value?.select()
  }
  
  // ESC para limpiar búsqueda
  if (e.key === 'Escape') {
    if (document.activeElement === inputRef.value) {
      clearSearch()
      inputRef.value?.blur()
    }
  }
  
  // Enter para ir a siguiente coincidencia (solo en modo página)
  if (e.key === 'Enter' && document.activeElement === inputRef.value) {
    e.preventDefault()
    if (e.shiftKey) {
      goToPreviousMatch()
    } else {
      goToNextMatch()
    }
  }
}

function handleInput(e) {
  const value = e.target.value
  localValue.value = value
  emit('update:modelValue', value)
  emit('search', value)
  highlightMatches(value)
}

function highlightMatches(query) {
  // Limpiar resaltados anteriores
  clearHighlights()
  
  if (!query || query.length < 2) {
    totalMatches.value = 0
    currentMatchIndex.value = 0
    return
  }
  
  // Buscar en toda la página, excluyendo nav, header con search y scripts
  const searchRoot = document.body
  const walker = document.createTreeWalker(
    searchRoot,
    NodeFilter.SHOW_TEXT,
    {
      acceptNode: (node) => {
        // Ignorar scripts, estilos y componentes de búsqueda
        let current = node.parentElement
        while (current) {
          const tag = current.tagName
          if (tag === 'SCRIPT' || tag === 'STYLE') {
            return NodeFilter.FILTER_REJECT
          }
          if (current.closest('.search-bar-component')) {
            return NodeFilter.FILTER_REJECT
          }
          current = current.parentElement
        }
        
        const text = node.textContent.trim()
        // Solo si tiene contenido significativo
        if (text.length === 0) return NodeFilter.FILTER_REJECT
        
        return text.toLowerCase().includes(query.toLowerCase())
          ? NodeFilter.FILTER_ACCEPT
          : NodeFilter.FILTER_REJECT
      }
    }
  )
  
  const nodesToHighlight = []
  let node
  while (node = walker.nextNode()) {
    nodesToHighlight.push(node)
  }
  
  let matchCount = 0
  nodesToHighlight.forEach(textNode => {
    const parent = textNode.parentElement
    const text = textNode.textContent
    const regex = new RegExp(`(${escapeRegex(query)})`, 'gi')
    
    if (regex.test(text)) {
      const fragment = document.createDocumentFragment()
      let lastIndex = 0
      
      text.replace(regex, (match, p1, offset) => {
        // Texto antes del match
        if (offset > lastIndex) {
          fragment.appendChild(document.createTextNode(text.slice(lastIndex, offset)))
        }
        
        // Match resaltado
        const mark = document.createElement('mark')
        mark.className = 'search-highlight'
        mark.style.backgroundColor = 'var(--color-accent, #fef08a)'
        mark.style.color = 'var(--color-neutral, #000)'
        mark.style.padding = '2px 4px'
        mark.style.borderRadius = '3px'
        mark.setAttribute('data-match-index', matchCount)
        mark.textContent = match
        fragment.appendChild(mark)
        
        matchCount++
        lastIndex = offset + match.length
        return match
      })
      
      // Texto después del último match
      if (lastIndex < text.length) {
        fragment.appendChild(document.createTextNode(text.slice(lastIndex)))
      }
      
      parent.replaceChild(fragment, textNode)
    }
  })
  
  totalMatches.value = matchCount
  currentMatchIndex.value = matchCount > 0 ? 1 : 0
  
  if (matchCount > 0) {
    highlightCurrentMatch()
  }
}

function clearHighlights() {
  document.querySelectorAll('.search-highlight').forEach(mark => {
    const parent = mark.parentNode
    parent.replaceChild(document.createTextNode(mark.textContent), mark)
    parent.normalize()
  })
}

function highlightCurrentMatch() {
  // Quitar resaltado actual
  document.querySelectorAll('.search-highlight').forEach(mark => {
    mark.style.backgroundColor = 'var(--color-accent, #fef08a)'
    mark.style.fontWeight = 'normal'
  })
  
  // Resaltar coincidencia actual
  const currentMark = document.querySelector(`.search-highlight[data-match-index="${currentMatchIndex.value - 1}"]`)
  if (currentMark) {
    currentMark.style.backgroundColor = 'var(--color-secondary, #fb923c)'
    currentMark.style.fontWeight = 'bold'
    currentMark.scrollIntoView({ behavior: 'smooth', block: 'center' })
  }
}

function goToNextMatch() {
  if (totalMatches.value === 0) return
  currentMatchIndex.value = currentMatchIndex.value >= totalMatches.value ? 1 : currentMatchIndex.value + 1
  highlightCurrentMatch()
}

function goToPreviousMatch() {
  if (totalMatches.value === 0) return
  currentMatchIndex.value = currentMatchIndex.value <= 1 ? totalMatches.value : currentMatchIndex.value - 1
  highlightCurrentMatch()
}

function clearSearch() {
  localValue.value = ''
  emit('update:modelValue', '')
  emit('search', '')
  clearHighlights()
  totalMatches.value = 0
  currentMatchIndex.value = 0
  inputRef.value?.focus()
}

function escapeRegex(str) {
  return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
}

function closeDropdown() {
  showDropdown.value = false
}

watch(() => props.modelValue, (newVal) => {
  localValue.value = newVal || ''
  if (searchMode.value === 'page') {
    if (newVal) {
      highlightMatches(newVal)
    } else {
      clearHighlights()
    }
  }
})

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
  if (props.autofocus) {
    inputRef.value?.focus()
  }
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  clearHighlights()
})
</script>

<template>
  <div class="relative search-bar-component">
    <div class="relative flex items-center gap-2">
      <!-- Icono de búsqueda -->
      <div class="absolute left-3 pointer-events-none">
        <svg class="w-4 h-4" :style="{ color: 'var(--color-text)', opacity: 0.5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      
      <!-- Input de búsqueda -->
      <input
        ref="inputRef"
        :value="localValue"
        @input="handleInput"
        type="text"
        :placeholder="placeholder"
        class="border rounded px-3 py-2 pl-10 pr-10 w-48 text-sm focus:outline-none focus:ring-2 transition"
        :style="{ backgroundColor: 'var(--color-base)', borderColor: 'var(--color-secondary)', color: 'var(--color-text)', '--tw-ring-color': 'var(--color-primary)' }"
      />
      
      <!-- Botón de limpiar (solo si hay texto) -->
      <button
        v-if="localValue"
        @click="clearSearch"
        type="button"
        class="absolute right-2 hover:opacity-70 transition"
        :style="{ color: 'var(--color-text)' }"
        title="Limpiar (ESC)"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      
      <!-- Contador de coincidencias -->
      <div v-if="totalMatches > 0" class="flex items-center gap-1 px-2 py-1 rounded text-xs whitespace-nowrap" :style="{ backgroundColor: 'var(--color-primary)', color: 'var(--color-base)' }">
        <span>{{ currentMatchIndex }}/{{ totalMatches }}</span>
        <div class="flex gap-0.5">
          <button @click="goToPreviousMatch" class="hover:opacity-70 p-0.5" title="Anterior (Shift+Enter)">
            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          <button @click="goToNextMatch" class="hover:opacity-70 p-0.5" title="Siguiente (Enter)">
            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Tooltip de ayuda -->
    <div v-if="localValue" class="absolute right-0 mt-1 text-xs z-10" :style="{ color: 'var(--color-text)', opacity: 0.7 }">
      <kbd class="px-1 rounded text-xs" :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-base)' }">Enter</kbd> 
      <kbd class="px-1 rounded text-xs" :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-base)' }">Shift+Enter</kbd> 
      <kbd class="px-1 rounded text-xs" :style="{ backgroundColor: 'var(--color-secondary)', color: 'var(--color-base)' }">ESC</kbd>
    </div>
  </div>
</template>

<style scoped>
kbd {
  padding: 2px 6px;
  border-radius: 3px;
  font-weight: 600;
}
</style>