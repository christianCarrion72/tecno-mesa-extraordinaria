export const index = () => ({ url: '/marcas' });

export const create = () => ({ url: '/marcas/create' });

export const store = () => ({ url: '/marcas' });

export const edit = (id: number | string) => ({
  url: `/marcas/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/marcas/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/marcas/${id}`,
});

