export const index = () => ({ url: '/orden-trabajos' });

export const create = () => ({ url: '/orden-trabajos/create' });

export const store = () => ({ url: '/orden-trabajos' });

export const show = (id: number | string) => ({
  url: `/orden-trabajos/${id}`,
});

export const edit = (id: number | string) => ({
  url: `/orden-trabajos/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/orden-trabajos/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/orden-trabajos/${id}`,
});
