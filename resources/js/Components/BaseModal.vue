<script setup lang="ts">
import { computed, onMounted, onUnmounted, watch } from 'vue';
import { X } from 'lucide-vue-next';

/**
 * BaseModal — Reusable modal overlay matching DESIGN_SYSTEM.md rules.
 *
 * Rules:
 *   - Backdrop: bg-neutral-900/50 backdrop-blur-sm fixed inset-0 z-50
 *   - Container: bg-white rounded-xl border border-neutral-200 p-6 shadow-xl w-full max-w-lg mx-auto mt-20
 *   - Header: Must include a clear title (text-lg font-medium) and an "X" close button (Ghost Button style)
 *   - Footer: Actions pinned to the bottom. Cancel on the left (Secondary Button), Confirm on the right (Primary/Danger)
 */

type ModalMaxWidth = 'sm' | 'md' | 'lg' | 'xl' | '2xl';

interface BaseModalProps {
  show: boolean;
  title?: string;
  subtitle?: string;
  maxWidth?: ModalMaxWidth;
  closeable?: boolean;
}

const props = withDefaults(defineProps<BaseModalProps>(), {
  show: false,
  title: undefined,
  subtitle: undefined,
  maxWidth: 'lg',
  closeable: true,
});

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const maxWidthClasses: Record<ModalMaxWidth, string> = {
  sm: 'max-w-sm',
  md: 'max-w-md',
  lg: 'max-w-lg',
  xl: 'max-w-xl',
  '2xl': 'max-w-2xl',
};

function handleClose() {
  if (props.closeable) {
    emit('close');
  }
}

function handleKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape' && props.show && props.closeable) {
    handleClose();
  }
}

watch(
  () => props.show,
  (isOpen) => {
    if (typeof document !== 'undefined') {
      if (isOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  }
);

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown);
  }
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
  }
});
</script>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
      >
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-neutral-900/50 backdrop-blur-sm transition-opacity"
          @click="handleClose"
        />

        <!-- Modal Container -->
        <div
          class="relative w-full overflow-hidden rounded-xl border border-border bg-surface shadow-xl transition-all my-8"
          :class="maxWidthClasses[maxWidth]"
          role="dialog"
          aria-modal="true"
        >
          <!-- Header -->
          <div
            v-if="title || $slots.header || closeable"
            class="flex items-start justify-between gap-4 border-b border-border px-6 py-4"
          >
            <div>
              <slot name="header">
                <h3
                  v-if="title"
                  class="text-lg font-medium text-foreground leading-snug"
                >
                  {{ title }}
                </h3>
                <p
                  v-if="subtitle"
                  class="text-xs text-muted mt-0.5"
                >
                  {{ subtitle }}
                </p>
              </slot>
            </div>

            <!-- Ghost Close Button -->
            <button
              v-if="closeable"
              type="button"
              class="rounded-lg p-2 text-muted transition-colors hover:bg-neutral-100 hover:text-neutral-900 dark:hover:bg-neutral-700 dark:hover:text-neutral-100 focus-visible:outline-none"
              aria-label="Close dialog"
              @click="handleClose"
            >
              <X class="h-5 w-5" />
            </button>
          </div>

          <!-- Body Content -->
          <div class="p-6 max-h-[calc(100vh-14rem)] overflow-y-auto">
            <slot />
          </div>

          <!-- Footer Actions -->
          <div
            v-if="$slots.footer"
            class="flex items-center justify-between border-t border-border px-6 py-4 bg-neutral-50/50 dark:bg-neutral-800/50"
          >
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
