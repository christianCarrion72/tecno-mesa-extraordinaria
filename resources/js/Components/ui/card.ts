import { defineComponent, h } from 'vue';

function mergeClass(base: string, extra: unknown): string {
  return [base, typeof extra === 'string' ? extra : ''].filter(Boolean).join(' ');
}

export const Card = defineComponent({
  name: 'UiCard',
  setup(_, { slots, attrs }) {
    return () =>
      h(
        'div',
        {
          ...attrs,
          class: mergeClass(
            'rounded-lg border bg-white shadow-sm dark:bg-transparent dark:border-gray-800',
            (attrs as any).class,
          ),
        },
        slots.default ? slots.default() : [],
      );
  },
});

export const CardHeader = defineComponent({
  name: 'UiCardHeader',
  setup(_, { slots, attrs }) {
    return () =>
      h(
        'div',
        {
          ...attrs,
          class: mergeClass('border-b px-6 py-4', (attrs as any).class),
        },
        slots.default ? slots.default() : [],
      );
  },
});

export const CardTitle = defineComponent({
  name: 'UiCardTitle',
  setup(_, { slots, attrs }) {
    return () =>
      h(
        'h2',
        {
          ...attrs,
          class: mergeClass(
            'text-lg font-semibold leading-none tracking-tight',
            (attrs as any).class,
          ),
        },
        slots.default ? slots.default() : [],
      );
  },
});

export const CardDescription = defineComponent({
  name: 'UiCardDescription',
  setup(_, { slots, attrs }) {
    return () =>
      h(
        'p',
        {
          ...attrs,
          class: mergeClass(
            'text-sm text-gray-500 dark:text-gray-400',
            (attrs as any).class,
          ),
        },
        slots.default ? slots.default() : [],
      );
  },
});

export const CardContent = defineComponent({
  name: 'UiCardContent',
  setup(_, { slots, attrs }) {
    return () =>
      h(
        'div',
        {
          ...attrs,
          class: mergeClass('px-6 py-4', (attrs as any).class),
        },
        slots.default ? slots.default() : [],
      );
  },
});

