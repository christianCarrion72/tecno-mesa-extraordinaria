export const index = () => ({ url: '/plan-pagos' });

export const show = (id: number | string) => ({
  url: `/plan-pagos/${id}`,
});

export const create = (params: { ordenTrabajo: number | string }) => ({
  url: `/orden-trabajos/${params.ordenTrabajo}/plan-pagos/create`,
});

export const store = (params: { ordenTrabajo: number | string }) => ({
  url: `/orden-trabajos/${params.ordenTrabajo}/plan-pagos`,
});

export const edit = (id: number | string) => ({
  url: `/plan-pagos/${id}/edit`,
});

export const update = (id: number | string) => ({
  url: `/plan-pagos/${id}`,
});

export const destroy = (id: number | string) => ({
  url: `/plan-pagos/${id}`,
});
