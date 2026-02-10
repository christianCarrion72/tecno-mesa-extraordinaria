import { defineComponent, h } from 'vue';

export const Separator = defineComponent({
  name: 'UiSeparator',
  setup(_, { attrs }) {
    return () =>
      h('hr', {
        ...attrs,
        class: [
          'my-4 border-t border-gray-200 dark:border-gray-700',
          (attrs as any).class ?? '',
        ]
          .filter(Boolean)
          .join(' '),
      });
  },
});

