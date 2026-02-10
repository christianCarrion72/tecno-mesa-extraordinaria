declare function route(name: string, params?: any): string;

export const dashboard = () => ({
  url: route('admin.dashboard'),
});

