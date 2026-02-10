<script setup lang="ts">
import Pagination from '@/Components/Pagination.vue';

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface Paginacion<T = any> {
  data: T[];
  from: number;
  to: number;
  total: number;
  links: PaginationLink[];
}

const props = defineProps<{
  paginator: Paginacion<any>;
}>();
</script>

<template>
  <Pagination :links="paginator.links" :meta="paginator" />
</template>

