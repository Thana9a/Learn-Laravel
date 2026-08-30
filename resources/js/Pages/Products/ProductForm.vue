<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import BaseCard from '@/Components/BaseCard.vue';
import CardHeader from '@/Components/CardHeader.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseSelect from '@/Components/BaseSelect.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseTextarea from '@/Components/BaseTextarea.vue';
import BaseCheckbox from '@/Components/BaseCheckbox.vue';
import { PRODUCT_DEFAULT_CATEGORY } from '@/Constants/product.constant';
import en from '@/lang/en';
import { Check, Plus, Star } from 'lucide-vue-next';

const props = defineProps<{
  categories: string[];
}>();

const emit = defineEmits<{
  (e: 'created'): void;
}>();

const t = en.product;

const form = useForm({
  name: '',
  category: props.categories[0] ?? PRODUCT_DEFAULT_CATEGORY,
  price: '',
  stock: 0,
  description: '',
  image_url: '',
  is_featured: false,
});

function submitProduct() {
  form.post(route('products.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      form.category = props.categories[0] ?? PRODUCT_DEFAULT_CATEGORY;
      emit('created');
    },
  });
}
</script>

<template>
  <BaseCard>
    <CardHeader
      :title="t.dashboard.form.title"
      :icon="Plus"
    />

    <form class="space-y-4" @submit.prevent="submitProduct">
      <BaseInput
        v-model="form.name"
        :label="t.dashboard.form.name"
        :error="form.errors.name"
        required
      />

      <BaseSelect
        v-model="form.category"
        :label="t.fields.category"
        :options="categories"
        :error="form.errors.category"
        required
      />

      <div class="grid grid-cols-2 gap-3">
        <BaseInput
          v-model="form.price"
          type="number"
          step="0.01"
          min="0.01"
          :label="t.fields.price"
          :error="form.errors.price"
          placeholder="0.00"
          required
        />

        <BaseInput
          v-model="form.stock"
          type="number"
          min="0"
          :label="t.fields.stock"
          :error="form.errors.stock"
          required
        />
      </div>

      <BaseTextarea
        v-model="form.description"
        :label="t.fields.description"
        :error="form.errors.description"
        :rows="3"
      />

      <BaseCheckbox
        v-model="form.is_featured"
        :label="t.dashboard.form.feature"
        :icon="Star"
      />

      <!-- Primary Action (Only one primary button in form) -->
      <BaseButton
        type="submit"
        variant="primary"
        size="lg"
        :icon="Check"
        :loading="form.processing"
        full-width
      >
        {{ form.processing ? t.dashboard.form.saving : t.dashboard.form.save }}
      </BaseButton>
    </form>
  </BaseCard>
</template>
