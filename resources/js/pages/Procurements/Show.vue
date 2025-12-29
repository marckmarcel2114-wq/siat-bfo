<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, CheckCircle, XCircle, FileText, Download } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

const props = defineProps<{
    procurement: any;
    canAuthorize: boolean;
}>();

const authorizeForm = useForm({
    action: '', // 'authorize' or 'reject'
    authorization_document: null as File | null,
    rejection_reason: '',
});

const submitAuthorize = () => {
    authorizeForm.action = 'authorize';
    authorizeForm.post(route('procurements.authorize-request', props.procurement.id));
};

const submitReject = () => {
    if (!authorizeForm.rejection_reason) {
        alert('Debes ingresar una razón para el rechazo.');
        return;
    }
    authorizeForm.action = 'reject';
    authorizeForm.post(route('procurements.authorize-request', props.procurement.id));
};

const getStatusBadge = (status: string) => {
    switch(status) {
        case 'pending': return { label: 'Pendiente', class: 'bg-yellow-100 text-yellow-800 border-yellow-200' };
        case 'authorized': return { label: 'Autorizado', class: 'bg-green-100 text-green-800 border-green-200' };
        case 'rejected': return { label: 'Rechazado', class: 'bg-red-100 text-red-800 border-red-200' };
        default: return { label: status, class: 'bg-gray-100 text-gray-800' };
    }
};

const breadcrumbs = [
    { title: 'Inicio', href: route('dashboard') },
    { title: 'Adquisiciones', href: route('procurements.index') },
    { title: 'Detalle', href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Detalle de Solicitud" />

        <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('procurements.index')">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold tracking-tight text-foreground">Solicitud #{{ procurement.id }}</h1>
                        <Badge :class="getStatusBadge(procurement.status).class" variant="outline">{{ getStatusBadge(procurement.status).label }}</Badge>
                    </div>
                    <p class="text-muted-foreground">Solicitado por {{ procurement.requester.name }} - {{ procurement.city?.name }}</p>
                </div>
                 <!-- If Authorized and has Doc -->
                 <Button v-if="procurement.authorization_document_path" variant="outline" as-child>
                    <a :href="'/storage/' + procurement.authorization_document_path" target="_blank">
                        <Download class="mr-2 h-4 w-4" /> Descargar Autorización
                    </a>
                </Button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Request Details -->
                <div class="lg:col-span-2 space-y-6">
                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Ítems Solicitados</CardTitle>
                        </CardHeader>
                        <CardContent class="p-0">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-muted/50 text-muted-foreground font-medium border-b border-border/50">
                                    <tr>
                                        <th class="p-3 w-16 text-center">Cant.</th>
                                        <th class="p-3">Descripción</th>
                                        <th class="p-3">Especificaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, i) in procurement.items" :key="i" class="border-b border-border/50 last:border-0 hover:bg-muted/10">
                                        <td class="p-3 text-center font-medium">{{ item.quantity }}</td>
                                        <td class="p-3">{{ item.name }}</td>
                                        <td class="p-3 text-muted-foreground">{{ item.specs }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </CardContent>
                    </Card>

                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Justificación</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm leading-relaxed">{{ procurement.justification }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Action Panel (Authorization) -->
                <div class="space-y-6">
                    <Card class="border-border/50 shadow-sm">
                        <CardHeader>
                            <CardTitle>Estado y Aprobación</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Helper Info -->
                            <div v-if="procurement.status === 'pending'" class="text-sm text-muted-foreground">
                                <p>Esta solicitud está pendiente de revisión por el Supervisor Nacional.</p>
                            </div>

                            <div v-else class="text-sm">
                                <p class="font-medium">Procesado por:</p>
                                <p class="text-muted-foreground mb-2">{{ procurement.authorizer?.name || 'Sistema' }}</p>
                                <p class="font-medium">Fecha:</p>
                                <p class="text-muted-foreground">{{ procurement.authorized_at || procurement.updated_at }}</p>
                            </div>

                            <!-- Authorization Form for Admin -->
                            <div v-if="canAuthorize && procurement.status === 'pending'" class="border-t border-border/50 pt-4 space-y-4">
                                <h4 class="font-medium text-foreground">Acciones de Supervisor</h4>
                                
                                <div class="space-y-2">
                                    <Label>Documento de Autorización Firmado (PDF)</Label>
                                    <Input 
                                        type="file" 
                                        accept=".pdf"
                                        @input="authorizeForm.authorization_document = ($event.target as HTMLInputElement).files?.[0] || null"
                                    />
                                    <p v-if="authorizeForm.errors.authorization_document" class="text-xs text-destructive">{{ authorizeForm.errors.authorization_document }}</p>
                                </div>
                                
                                <Button 
                                    @click="submitAuthorize" 
                                    :disabled="authorizeForm.processing || !authorizeForm.authorization_document" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white"
                                >
                                    <CheckCircle class="mr-2 h-4 w-4" /> Aprobar Solicitud
                                </Button>

                                <div class="relative flex py-2 items-center">
                                    <div class="flex-grow border-t border-border/50"></div>
                                    <span class="flex-shrink-0 mx-4 text-xs text-muted-foreground">O RECHAZAR</span>
                                    <div class="flex-grow border-t border-border/50"></div>
                                </div>

                                <div class="space-y-2">
                                    <Label>Razón del Rechazo</Label>
                                    <Textarea v-model="authorizeForm.rejection_reason" placeholder="Motivo de rechazo..." rows="2" />
                                </div>

                                <Button 
                                    @click="submitReject" 
                                    :disabled="authorizeForm.processing || !authorizeForm.rejection_reason" 
                                    variant="destructive"
                                    class="w-full"
                                >
                                    <XCircle class="mr-2 h-4 w-4" /> Rechazar Solicitud
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
