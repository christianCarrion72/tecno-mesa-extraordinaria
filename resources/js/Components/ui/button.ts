import { defineComponent, h } from 'vue';

type Variant = 'default' | 'outline' | 'destructive';
type Size = 'default' | 'sm' | 'lg';

const baseClass =
  'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';

const variantClasses: Record<Variant, string> = {
  default:
    'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:ring-indigo-600',
  outline:
    'border border-gray-300 bg-white text-gray-800 hover:bg-gray-50 focus-visible:ring-gray-300',
  destructive:
    'bg-red-600 text-white hover:bg-red-700 focus-visible:ring-red-600',
};

const sizeClasses: Record<Size, string> = {
  default: 'h-10 px-4 py-2',
  sm: 'h-9 px-3',
  lg: 'h-11 px-8',
};

export const Button = defineComponent({
  name: 'UiButton',
  props: {
    type: {
      type: String as () => 'button' | 'submit' | 'reset',
      default: 'button',
    },
    variant: {
      type: String as () => Variant,
      default: 'default',
    },
    size: {
      type: String as () => Size,
      default: 'default',
    },
  },
  setup(props, { slots, attrs }) {
    return () =>
      h(
        'button',
        {
          ...attrs,
          type: props.type,
          class: [
            baseClass,
            variantClasses[props.variant] ?? variantClasses.default,
            sizeClasses[props.size] ?? sizeClasses.default,
            (attrs as any).class ?? '',
          ]
            .filter(Boolean)
            .join(' '),
        },
        slots.default ? slots.default() : [],
      );
  },
});

