export const index = () => ({ url: '/usuarios' });

export const create = () => ({ url: '/usuarios/create' });

export const store = () => ({ url: '/usuarios' });

export const edit = (id: number | string) => ({
  url: `/usuarios/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/usuarios/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/usuarios/${id}`,
});

