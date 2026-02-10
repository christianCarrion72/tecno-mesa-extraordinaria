export const index = () => ({ url: '/partes' });

export const create = () => ({ url: '/partes/create' });

export const store = () => ({ url: '/partes' });

export const edit = (id: number | string) => ({
  url: `/partes/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/partes/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/partes/${id}`,
});

