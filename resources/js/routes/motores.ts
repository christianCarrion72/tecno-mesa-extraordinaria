export const index = () => ({ url: '/motores' });

export const create = () => ({ url: '/motores/create' });

export const store = () => ({ url: '/motores' });

export const edit = (id: number | string) => ({
  url: `/motores/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/motores/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/motores/${id}`,
});

