<template>
  <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="flex flex-1 justify-between sm:hidden">
      <Link
        :href="links[0].url"
        :class="[
          'relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50',
          !links[0].url && 'opacity-50 cursor-not-allowed'
        ]"
        preserve-state
      >
        Anterior
      </Link>
      <Link
        :href="links[links.length - 1].url"
        :class="[
          'relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50',
          !links[links.length - 1].url && 'opacity-50 cursor-not-allowed'
        ]"
        preserve-state
      >
        Siguiente
      </Link>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Mostrando
          <span class="font-medium">{{ from }}</span>
          a
          <span class="font-medium">{{ to }}</span>
          de
          <span class="font-medium">{{ total }}</span>
          resultados
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <Link
            v-for="(link, index) in links"
            :key="index"
            :href="link.url"
            :class="[
              'relative inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20',
              index === 0 ? 'rounded-l-md' : '',
              index === links.length - 1 ? 'rounded-r-md' : '',
              link.active
                ? 'z-10 bg-taller-blue-dark text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-taller-blue-dark'
                : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
              !link.url && 'opacity-50 cursor-not-allowed'
            ]"
            v-html="link.label"
            preserve-state
          />
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  links: Array,
  meta: Object
})

const from = computed(() => props.meta?.from || 0)
const to = computed(() => props.meta?.to || 0)
const total = computed(() => props.meta?.total || 0)
</script>
