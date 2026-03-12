import { defineComponent, h } from 'vue';

export const Label = defineComponent({
  name: 'UiLabel',
  setup(_, { slots, attrs }) {
    return () =>
      h(
        'label',
        {
          ...attrs,
          class: [
            'block text-sm font-medium text-foreground',
            (attrs as any).class ?? '',
          ]
            .filter(Boolean)
            .join(' '),
        },
        slots.default ? slots.default() : [],
      );
  },
});

