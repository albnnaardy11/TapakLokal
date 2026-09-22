<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import PanelLayout from '../../Components/Admin/PanelLayout.vue';
import Pagination from '../../Components/Admin/Pagination.vue';
const props = defineProps({ mode: String, records: Object, roles: Array, permissions: Array, navigation: Array });
const page = usePage();
const editing = ref(false);
const roleId = ref(null);
const form = useForm({ id: null, name: '', email: '', password: '', status: 'active', roles: [] });
const roleForm = useForm({ permissions: [] });
const canManage = computed(() => page.props.auth.permissions.includes(props.mode === 'users' ? 'users.manage' : 'roles.manage'));
const edit = item => {
    if (props.mode === 'users') { form.id = item?.id ?? null; form.name = item?.name || ''; form.email = item?.email || ''; form.password = ''; form.status = item?.status || 'active'; form.roles = item?.roles.map(role => role.id) || []; }
    else { roleId.value = item.id; roleForm.permissions = item.permissions.map(permission => permission.id); }
    editing.value = true;
};
const submit = () => props.mode === 'users' ? form.post(route('admin.access.users.save'), { onSuccess: () => { editing.value = false; form.reset(); } }) : roleForm.put(route('admin.access.roles.save', roleId.value), { onSuccess: () => editing.value = false });
</script>
<template>
    <PanelLayout :title="mode === 'users' ? 'Pengguna & Admin' : 'Roles & Permissions'" subtitle="Kelola siapa yang dapat mengakses dan menjalankan setiap tindakan." :navigation="navigation">
        <template #actions><button v-if="mode === 'users' && canManage" class="panel-primary" @click="edit(null)">Tambah pengguna</button></template>
        <form v-if="editing" class="panel-surface mb-6 p-6" @submit.prevent="submit">
            <div v-if="mode === 'users'" class="grid gap-4 sm:grid-cols-2"><label class="text-xs font-semibold">Nama<input v-model="form.name" required class="panel-input mt-2" /></label><label class="text-xs font-semibold">Email<input v-model="form.email" required type="email" class="panel-input mt-2" /></label><label class="text-xs font-semibold">Password {{ form.id ? '(kosongkan agar tetap)' : '' }}<input v-model="form.password" :required="!form.id" type="password" autocomplete="new-password" class="panel-input mt-2" /></label><label class="text-xs font-semibold">Status<select v-model="form.status" class="panel-input mt-2"><option>active</option><option>suspended</option></select></label><fieldset class="sm:col-span-2"><legend class="mb-3 text-xs font-semibold">Role pengguna</legend><div class="flex flex-wrap gap-4"><label v-for="role in roles" :key="role.id" class="flex items-center gap-2 text-xs"><input v-model="form.roles" :value="role.id" type="checkbox" />{{ role.label }}</label></div></fieldset></div>
            <div v-else class="grid gap-3 sm:grid-cols-3"><label v-for="permission in permissions" :key="permission.id" class="flex items-center gap-2 rounded-lg bg-slate-50 p-3 text-xs"><input v-model="roleForm.permissions" :value="permission.id" type="checkbox" />{{ permission.name }}</label></div>
            <div class="mt-6 flex gap-2"><button class="panel-primary" :disabled="form.processing || roleForm.processing">Simpan akses</button><button type="button" class="panel-secondary" @click="editing = false">Batal</button></div>
        </form>
        <section class="panel-surface"><div v-for="item in records.data" :key="item.id" class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 p-5"><div><p class="text-sm font-bold">{{ item.label || item.name }}</p><p class="mt-1 text-xs text-slate-500">{{ mode === 'users' ? item.email : item.name }}</p><div class="mt-3 flex flex-wrap gap-1"><span v-for="tag in (mode === 'users' ? item.roles : item.permissions)" :key="tag.id" class="rounded-md bg-blue-50 px-2 py-1 text-[10px] text-blue-700">{{ tag.label || tag.name }}</span></div></div><div class="flex items-center gap-3"><span v-if="item.status" class="text-xs text-slate-500">{{ item.status }}</span><button v-if="canManage && item.name !== 'super_admin'" class="panel-secondary" @click="edit(item)">Kelola akses</button></div></div><Pagination :records="records" /></section>
    </PanelLayout>
</template>

