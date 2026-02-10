export const index = () => ({ url: '/modelos' });

export const create = () => ({ url: '/modelos/create' });

export const store = () => ({ url: '/modelos' });

export const edit = (id: number | string) => ({
  url: `/modelos/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/modelos/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/modelos/${id}`,
});

