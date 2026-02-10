<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
  breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
  <AdminLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div
          v-if="props.breadcrumbs && props.breadcrumbs.length"
          class="flex items-center text-sm text-gray-500 space-x-2"
        >
          <template
            v-for="(crumb, index) in props.breadcrumbs"
            :key="index"
          >
            <Link
              :href="crumb.href"
              class="hover:text-gray-900 dark:hover:text-gray-100"
            >
              {{ crumb.title }}
            </Link>
            <span
              v-if="index < props.breadcrumbs.length - 1"
              class="text-gray-400"
            >
              /
            </span>
          </template>
        </div>
      </div>
    </template>

    <slot />
  </AdminLayout>
</template>

