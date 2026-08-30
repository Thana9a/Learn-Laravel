<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseText from '@/Components/BaseText.vue';
import BaseCard from '@/Components/BaseCard.vue';
import CardFooter from '@/Components/CardFooter.vue';
import BaseBadge from '@/Components/BaseBadge.vue';
import BaseButton from '@/Components/BaseButton.vue';
import type { OrderDetail } from '@/Types/order.type';
import { useOrderFormat } from '@/Composables/useOrderFormat';
import en from '@/lang/en';
import { ArrowLeft, Printer, Calendar, User, Clock, Receipt } from 'lucide-vue-next';

const props = defineProps<{
  order: OrderDetail;
}>();

const t = en.order;
const common = en.common;
const { money: formatCurrency, formatFullDate: formatDate } = useOrderFormat();

function printReceipt() {
  window.print();
}
</script>

<template>
  <Head :title="`${t.order_number} ${order.order_number}`" />

  <AuthenticatedLayout>
    <div class="space-y-6 p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
      <!-- Top Action Bar -->
      <div class="flex items-center justify-between gap-4">
        <BaseButton
          variant="secondary"
          size="md"
          :icon="ArrowLeft"
          :href="route('orders.index')"
        >
          {{ common.actions.back }}
        </BaseButton>

        <div class="flex items-center gap-2">
          <BaseButton
            variant="secondary"
            size="md"
            :icon="Printer"
            @click="printReceipt"
          >
            {{ t.actions.print_receipt }}
          </BaseButton>
        </div>
      </div>

      <!-- Main Receipt Card -->
      <BaseCard padding="lg" class="space-y-6">
        <!-- Order Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-border pb-6 gap-4">
          <div>
            <div class="flex items-center gap-3">
              <h1 class="text-2xl font-bold font-mono text-foreground">
                {{ order.order_number }}
              </h1>
              <BaseBadge :status="order.status" />
            </div>
            <p class="text-xs text-muted mt-1 flex items-center gap-2">
              <Calendar class="h-3.5 w-3.5" />
              {{ formatDate(order.created_at) }}
            </p>
          </div>

          <div class="text-left sm:text-right">
            <p class="text-xs text-muted">{{ t.total_amount }}</p>
            <p class="text-2xl font-bold font-mono text-primary-600">
              {{ formatCurrency(order.total_amount) }}
            </p>
          </div>
        </div>

        <!-- Meta Details -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-lg bg-surface-muted/50 border border-border">
          <div>
            <BaseText as="span" size="xs" weight="medium" variant="muted">{{ t.customer }}</BaseText>
            <BaseText as="span" size="sm" weight="semibold">
              {{ order.customer_name || t.dashboard.table.walk_in }}
            </BaseText>
          </div>
          <div>
            <BaseText as="span" size="xs" weight="medium" variant="muted">{{ t.staff }}</BaseText>
            <BaseText as="span" size="sm" weight="semibold">
              {{ order.staff?.name || t.dashboard.table.unassigned }}
            </BaseText>
          </div>
          <div v-if="order.notes">
            <BaseText as="span" size="xs" weight="medium" variant="muted">{{ t.notes }}</BaseText>
            <BaseText as="span" size="sm">
              {{ order.notes }}
            </BaseText>
          </div>
        </div>

        <!-- Items Table -->
        <div>
          <BaseText as="h2" size="sm" weight="semibold" class="mb-3">
            {{ t.items }} ({{ order.items.reduce((s, i) => s + i.quantity, 0) }})
          </BaseText>

          <div class="overflow-x-auto rounded-lg border border-border">
            <table class="w-full text-left text-sm">
              <thead class="bg-surface-muted/60 text-xs text-muted border-b border-border">
                <tr>
                  <th class="px-4 py-3 font-medium">{{ t.items }}</th>
                  <th class="px-4 py-3 text-right font-medium">{{ t.quantity }}</th>
                  <th class="px-4 py-3 text-right font-medium">{{ t.unit_price }}</th>
                  <th class="px-4 py-3 text-right font-medium">{{ t.subtotal }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border text-neutral-800 dark:text-neutral-200">
                <tr v-for="item in order.items" :key="item.id" class="h-12">
                  <td class="px-4 py-3 font-medium">{{ item.product_name }}</td>
                  <td class="px-4 py-3 text-right font-mono">{{ item.quantity }}</td>
                  <td class="px-4 py-3 text-right font-mono text-muted">{{ formatCurrency(item.unit_price) }}</td>
                  <td class="px-4 py-3 text-right font-mono font-medium text-foreground">{{ formatCurrency(item.subtotal) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Summary Footer -->
        <CardFooter class="flex justify-end">
          <div class="w-full sm:w-64 space-y-2 text-sm">
            <div class="flex justify-between text-neutral-600 dark:text-neutral-400">
              <span>{{ t.subtotal }}</span>
              <span class="font-mono">{{ formatCurrency(order.items.reduce((s, i) => s + Number(i.subtotal), 0)) }}</span>
            </div>

            <div
              v-for="discount in order.order_discounts"
              :key="discount.id"
              class="flex justify-between text-success-600 dark:text-success-400"
            >
              <span>Discount ({{ discount.code_used }})</span>
              <span class="font-mono">-{{ formatCurrency(discount.amount_applied) }}</span>
            </div>

            <div class="flex justify-between border-t border-border pt-2 text-base font-bold text-foreground">
              <span>{{ t.total_amount }}</span>
              <span class="font-mono text-primary-600">{{ formatCurrency(order.total_amount) }}</span>
            </div>
          </div>
        </CardFooter>
      </BaseCard>
    </div>
  </AuthenticatedLayout>
</template>
