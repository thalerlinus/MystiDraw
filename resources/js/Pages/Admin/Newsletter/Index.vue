<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  total: Number,
  recent: Array,
});

const form = useForm({
  subject: '',
  message: '',
});

const sending = ref(false);

const submit = () => {
  if (sending.value) return;
  sending.value = true;
  form.post(route('admin.newsletter.send'), {
    preserveScroll: true,
    onFinish: () => { sending.value = false; },
    onSuccess: () => { form.reset('subject', 'message'); },
  });
};
</script>

<template>
  <AdminLayout title="Newsletter">
    <div class="space-y-10">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-navy-900">Newsletter Verwaltung</h1>
        <div class="text-sm text-navy-600">Abonnenten: <span class="font-semibold">{{ total }}</span></div>
      </div>

      <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-white shadow rounded-xl p-6 border border-navy-100">
          <h2 class="text-lg font-semibold mb-4 text-navy-800">E-Mail an alle senden</h2>
          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-navy-700 mb-1">Betreff</label>
              <input v-model="form.subject" type="text" class="w-full rounded-lg border-navy-300 focus:border-gold-400 focus:ring-gold-400/30" maxlength="150" required />
              <div class="text-xs text-navy-500 mt-1">Max 150 Zeichen</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-navy-700 mb-1">Nachricht (HTML erlaubt)</label>
              <textarea v-model="form.message" rows="10" class="w-full rounded-lg border-navy-300 focus:border-gold-400 focus:ring-gold-400/30 font-mono text-sm" maxlength="5000" required></textarea>
              <div class="text-xs text-navy-500 mt-1 flex justify-between"><span>Max 5000 Zeichen</span><span>{{ form.message.length }}/5000</span></div>
            </div>
            <div class="text-xs text-navy-500 bg-amber-50 border border-amber-200 rounded p-3">
              Der Abmelde-Link wird automatisch eingefügt. Versand läuft asynchron über die Queue.
            </div>
            <div class="flex gap-3">
              <button :disabled="sending || form.processing" type="submit" class="px-5 py-2.5 rounded-lg bg-navy-800 text-white font-semibold disabled:opacity-50 hover:bg-navy-700 flex items-center gap-2">
                <span v-if="sending || form.processing" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
                <span>Jetzt senden</span>
              </button>
            </div>
          </form>
        </div>

        <div class="bg-white shadow rounded-xl p-6 border border-navy-100">
          <h2 class="text-lg font-semibold mb-4 text-navy-800">Neueste Abonnenten</h2>
          <ul class="divide-y divide-navy-100">
            <li v-for="s in recent" :key="s.id" class="py-3 flex flex-col">
              <span class="font-medium text-navy-800">{{ s.name }}</span>
              <span class="text-xs text-navy-500">{{ s.email }}</span>
              <span class="text-[11px] text-navy-400 mt-1">seit {{ s.subscribed_at }}</span>
            </li>
            <li v-if="!recent.length" class="text-sm text-navy-500">Keine Abonnenten vorhanden.</li>
          </ul>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
