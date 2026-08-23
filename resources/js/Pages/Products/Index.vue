<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BaseCard from '@/Components/BaseCard.vue';
import en from '@/lang/en';
import {
  AlertTriangle,
  Boxes,
  Check,
  Filter,
  Package,
  Plus,
  Search,
  Star,
  Trash2,
  Wallet,
} from 'lucide-vue-next';

interface Product {
  id: number;
  name: string;
  category: string;
  price: number | string;
  stock: number;
  description?: string | null;
  image_url?: string | null;
  is_featured: boolean;
}

interface ProductStats {
  total_products: number;
  avg_price: number;
  low_stock_count: number;
  total_inventory: number;
}

interface Filters {
  search?: string;
  category?: string;
}

const props = withDefaults(defineProps<{
  products: Product[];
  stats: ProductStats;
  categories: string[];
  filters?: Filters;
}>(), {
  filters: () => ({ search: '', category: 'All' }),
});

const t = en.product;
const common = en.common;
const search = ref(props.filters.search ?? '');
const selectedCategory = ref(props.filters.category ?? 'All');

const form = useForm({
  name: '',
  category: props.categories[0] ?? 'Coffee',
  price: '',
  stock: 0,
  description: '',
  image_url: '',
  is_featured: false,
});

const categoryOptions = computed(() => ['All', ...props.categories]);
const lowStockProducts = computed(() => props.products.filter((product) => product.stock < 10).slice(0, 5));
const inStockCount = computed(() => props.products.filter((product) => product.stock > 0).length);

function money(value: number | string): string {
  return `$${Number(value).toFixed(2)}`;
}

function applyFilters() {
  router.get(
    route('products.index'),
    {
      search: search.value,
      category: selectedCategory.value,
    },
    {
      preserveState: true,
      replace: true,
    },
  );
}

function clearFilters() {
  search.value = '';
  selectedCategory.value = 'All';
  applyFilters();
}

function submitProduct() {
  form.post(route('products.store'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      form.category = props.categories[0] ?? 'Coffee';
    },
  });
}

function deleteProduct(product: Product) {
  if (!window.confirm(t.dashboard.confirm_delete.replace(':name', product.name))) {
    return;
  }

  router.delete(route('products.destroy', product.id), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head :title="t.title" />

  <AuthenticatedLayout>
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">{{ t.dashboard.eyebrow }}</p>
          <h1 class="mt-1 text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ t.title }}</h1>
          <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ t.dashboard.subtitle }}</p>
        </div>
        <a
          :href="route('pos.index')"
          class="inline-flex items-center justify-center gap-2 rounded-lg bg-neutral-100 px-4 py-2 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600"
        >
          <Package class="h-4 w-4" />
          {{ t.dashboard.open_pos }}
        </a>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <BaseCard>
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.total_products }}</p>
            <Package class="h-5 w-5 text-primary-500" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ stats.total_products }}</p>
          <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ inStockCount }} {{ t.dashboard.stats.in_stock }}</p>
        </BaseCard>

        <BaseCard>
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.inventory_units }}</p>
            <Boxes class="h-5 w-5 text-info-600" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ stats.total_inventory }}</p>
          <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.inventory_hint }}</p>
        </BaseCard>

        <BaseCard>
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.low_stock }}</p>
            <AlertTriangle class="h-5 w-5 text-warning-600" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ stats.low_stock_count }}</p>
          <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.low_stock_hint }}</p>
        </BaseCard>

        <BaseCard>
          <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.average_price }}</p>
            <Wallet class="h-5 w-5 text-success-600" />
          </div>
          <p class="mt-2 text-2xl font-semibold text-neutral-900 dark:text-neutral-100">{{ money(stats.avg_price) }}</p>
          <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ t.dashboard.stats.average_price_hint }}</p>
        </BaseCard>
      </div>

      <div class="grid gap-6 xl:grid-cols-3">
        <section class="space-y-4 xl:col-span-2">
          <BaseCard>
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
              <label class="flex-1">
                <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.dashboard.filters.search_label }}</span>
                <span class="relative block">
                  <Search class="pointer-events-none absolute left-3 top-3.5 h-4 w-4 text-neutral-500 dark:text-neutral-400" />
                  <input
                    v-model="search"
                    type="search"
                    class="h-12 w-full rounded-lg border border-neutral-200 bg-white pl-10 pr-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    :placeholder="t.placeholders.search"
                    @keyup.enter="applyFilters"
                  />
                </span>
              </label>

              <label class="w-full lg:w-56">
                <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.dashboard.filters.category_label }}</span>
                <select
                  v-model="selectedCategory"
                  class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                >
                  <option v-for="category in categoryOptions" :key="category" :value="category">{{ category }}</option>
                </select>
              </label>

              <div class="flex gap-2">
                <button
                  type="button"
                  class="inline-flex h-12 items-center gap-2 rounded-lg bg-primary-500 px-4 text-sm font-medium text-white transition-colors hover:bg-primary-600 active:bg-primary-700"
                  @click="applyFilters"
                >
                  <Filter class="h-4 w-4" />
                  {{ common.actions.apply }}
                </button>
                <button
                  type="button"
                  class="inline-flex h-12 items-center rounded-lg bg-neutral-100 px-4 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600"
                  @click="clearFilters"
                >
                  {{ common.actions.clear }}
                </button>
              </div>
            </div>
          </BaseCard>

          <BaseCard :flush="true">
            <div class="flex items-center justify-between border-b border-neutral-200 px-4 py-3 dark:border-neutral-700">
              <div>
                <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ t.dashboard.table.title }}</h2>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ products.length }} {{ t.dashboard.filters.match_count }}</p>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm">
                <thead class="sticky top-0 border-b border-neutral-200 bg-white text-xs text-neutral-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400">
                  <tr>
                    <th class="px-4 py-3 font-medium">{{ t.dashboard.table.product }}</th>
                    <th class="px-4 py-3 font-medium">{{ t.fields.category }}</th>
                    <th class="px-4 py-3 text-right font-medium">{{ t.fields.price }}</th>
                    <th class="px-4 py-3 text-right font-medium">{{ t.fields.stock }}</th>
                    <th class="px-4 py-3 text-center font-medium">{{ t.dashboard.table.status }}</th>
                    <th class="px-4 py-3 text-right font-medium">{{ t.dashboard.table.actions }}</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-neutral-700 dark:divide-neutral-700 dark:text-neutral-300">
                  <tr v-if="products.length === 0">
                    <td colspan="6" class="px-4 py-10 text-center text-neutral-500 dark:text-neutral-400">
                      {{ t.dashboard.table.empty }}
                    </td>
                  </tr>
                  <tr v-for="product in products" :key="product.id" class="h-12 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                    <td class="px-4 py-3">
                      <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ product.name }}</div>
                      <div class="max-w-sm truncate text-xs text-neutral-500 dark:text-neutral-400">{{ product.description || t.dashboard.table.no_description }}</div>
                    </td>
                    <td class="px-4 py-3">{{ product.category }}</td>
                    <td class="px-4 py-3 text-right font-mono font-medium text-neutral-900 dark:text-neutral-100">{{ money(product.price) }}</td>
                    <td class="px-4 py-3 text-right font-mono">{{ product.stock }}</td>
                    <td class="px-4 py-3 text-center">
                      <span
                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                        :class="product.stock === 0 ? 'bg-danger-50 text-danger-600' : product.stock < 10 ? 'bg-warning-50 text-warning-600' : 'bg-success-50 text-success-600'"
                      >
                        <span
                          class="h-1.5 w-1.5 rounded-full"
                          :class="product.stock === 0 ? 'bg-danger-500' : product.stock < 10 ? 'bg-warning-500' : 'bg-success-500'"
                        />
                        {{ product.stock === 0 ? t.dashboard.status.out : product.stock < 10 ? t.dashboard.status.low : t.dashboard.status.ready }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                      <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-500 transition-colors hover:bg-danger-50 hover:text-danger-600 dark:text-neutral-400 dark:hover:bg-danger-900/20"
                        :aria-label="`${t.delete_product} ${product.name}`"
                        @click="deleteProduct(product)"
                      >
                        <Trash2 class="h-4 w-4" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </BaseCard>
        </section>

        <aside class="space-y-6">
          <BaseCard>
            <div class="mb-4 flex items-center gap-2">
              <Plus class="h-5 w-5 text-primary-500" />
              <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ t.dashboard.form.title }}</h2>
            </div>

            <form class="space-y-4" @submit.prevent="submitProduct">
              <label class="block">
                <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.dashboard.form.name }}</span>
                <input
                  v-model="form.name"
                  type="text"
                  class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                />
                <span v-if="form.errors.name" class="mt-1 block text-xs text-danger-600">{{ form.errors.name }}</span>
              </label>

              <label class="block">
                <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.fields.category }}</span>
                <select
                  v-model="form.category"
                  class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                >
                  <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                </select>
                <span v-if="form.errors.category" class="mt-1 block text-xs text-danger-600">{{ form.errors.category }}</span>
              </label>

              <div class="grid grid-cols-2 gap-3">
                <label class="block">
                  <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.fields.price }}</span>
                  <input
                    v-model="form.price"
                    type="number"
                    min="0.01"
                    step="0.01"
                    class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                  />
                  <span v-if="form.errors.price" class="mt-1 block text-xs text-danger-600">{{ form.errors.price }}</span>
                </label>

                <label class="block">
                  <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.fields.stock }}</span>
                  <input
                    v-model="form.stock"
                    type="number"
                    min="0"
                    class="h-12 w-full rounded-lg border border-neutral-200 bg-white px-4 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                  />
                  <span v-if="form.errors.stock" class="mt-1 block text-xs text-danger-600">{{ form.errors.stock }}</span>
                </label>
              </div>

              <label class="block">
                <span class="mb-1 block text-xs font-medium text-neutral-700 dark:text-neutral-300">{{ t.fields.description }}</span>
                <textarea
                  v-model="form.description"
                  rows="3"
                  class="w-full rounded-lg border border-neutral-200 bg-white px-4 py-3 text-sm text-neutral-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                />
              </label>

              <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border border-neutral-200 bg-neutral-50 px-4 text-sm font-medium text-neutral-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">
                <input v-model="form.is_featured" type="checkbox" class="rounded border-neutral-300 text-primary-500 focus:ring-primary-500" />
                <Star class="h-4 w-4 text-primary-500" />
                {{ t.dashboard.form.feature }}
              </label>

              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-primary-500 px-4 text-sm font-medium text-white transition-colors hover:bg-primary-600 active:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <Check class="h-4 w-4" />
                {{ form.processing ? t.dashboard.form.saving : t.dashboard.form.save }}
              </button>
            </form>
          </BaseCard>

          <BaseCard>
            <div class="mb-4 flex items-center gap-2">
              <AlertTriangle class="h-5 w-5 text-warning-600" />
              <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ t.dashboard.restock.title }}</h2>
            </div>

            <div v-if="lowStockProducts.length === 0" class="rounded-lg bg-success-50 p-4 text-sm text-success-600 dark:bg-success-900/20 dark:text-success-400">
              {{ t.dashboard.restock.empty }}
            </div>
            <div v-else class="space-y-3">
              <div v-for="product in lowStockProducts" :key="product.id" class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                  <p class="truncate text-sm font-medium text-neutral-900 dark:text-neutral-100">{{ product.name }}</p>
                  <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ product.category }}</p>
                </div>
                <span class="rounded-full bg-warning-50 px-2.5 py-0.5 text-xs font-medium text-warning-600 dark:bg-warning-900/20">
                  {{ product.stock }} {{ t.dashboard.restock.left }}
                </span>
              </div>
            </div>
          </BaseCard>
        </aside>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
