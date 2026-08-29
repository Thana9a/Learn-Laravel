<script setup lang="ts">
import BaseCard from '@/Components/BaseCard.vue';
import BaseInput from '@/Components/BaseInput.vue';
import BaseButton from '@/Components/BaseButton.vue';
import BaseText from '@/Components/BaseText.vue';
import CardHeader from '@/Components/CardHeader.vue';
import CardContent from '@/Components/CardContent.vue';
import { useReportForm } from '@/Composables/useReportForm';
import en from '@/lang/en';
import { FileText, Send } from 'lucide-vue-next';

const emit = defineEmits<{
  (e: 'success'): void;
  (e: 'error'): void;
}>();

const t = en.report;
const { form, reportTypes, submitGenerate } = useReportForm();

function onSubmit() {
  submitGenerate({
    onSuccess: () => emit('success'),
    onError: () => emit('error'),
  });
}
</script>

<template>
  <BaseCard>
    <CardHeader
      :title="t.dashboard.form.title"
      :subtitle="t.dashboard.form.subtitle"
      :icon="FileText"
    />

    <CardContent>
      <form class="space-y-4" @submit.prevent="onSubmit">
      <BaseInput
        v-model="form.title"
        :label="t.dashboard.form.report_title"
        :placeholder="t.dashboard.form.placeholder"
        :error="form.errors.title"
        required
      />

      <div>
        <BaseText as="span" size="xs" weight="medium" class="mb-2">{{ t.dashboard.form.type }}</BaseText>
        <div class="space-y-2">
          <label
            v-for="reportType in reportTypes"
            :key="reportType.value"
            class="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 text-sm transition-colors"
            :class="form.type === reportType.value ? 'border-primary-500 bg-primary-50 text-primary-700 ring-1 ring-primary-500 dark:bg-primary-900/20' : 'border-border bg-neutral-50 text-body hover:bg-neutral-100 dark:bg-neutral-800 dark:hover:bg-neutral-700'"
          >
            <input v-model="form.type" type="radio" :value="reportType.value" class="sr-only" />
            <component :is="reportType.icon" class="h-4 w-4" />
            <span class="min-w-0">
              <BaseText as="span" weight="medium">{{ reportType.label }}</BaseText>
              <BaseText as="span" size="xs" variant="muted">{{ reportType.description }}</BaseText>
            </span>
          </label>
        </div>
        <BaseText v-if="form.errors.type" size="xs" variant="error" class="mt-1">{{ form.errors.type }}</BaseText>
      </div>

      <BaseButton
        type="submit"
        variant="primary"
        size="lg"
        :icon="Send"
        :loading="form.processing"
        full-width
      >
        {{ form.processing ? t.dashboard.form.queuing : t.dashboard.form.generate }}
      </BaseButton>
      </form>
    </CardContent>
  </BaseCard>
</template>
