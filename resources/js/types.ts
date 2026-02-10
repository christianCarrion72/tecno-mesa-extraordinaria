export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface Rol {
  id: number;
  nombre: string;
  descripcion?: string | null;
}

export interface Usuario {
  id: number;
  name: string;
  email: string;
  rol_id?: number | null;
  telefono?: string | null;
  direccion?: string | null;
  foto?: string | null;
  rol?: Rol | null;
}

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

export interface Paginacion<T> {
  data: T[];
  current_page?: number;
  from: number;
  to: number;
  total: number;
  per_page?: number;
  last_page?: number;
  links: PaginationLink[];
}

