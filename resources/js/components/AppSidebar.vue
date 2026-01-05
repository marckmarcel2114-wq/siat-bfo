<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarGroup,
    SidebarGroupLabel,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, MapPin, Building, Monitor, ShoppingCart, Wrench, Settings2, Activity, ShieldAlert, Disc } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Usuarios',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Ciudades',
        href: '/cities',
        icon: MapPin,
    },
    {
        title: 'Sucursales',
        href: '/branches',
        icon: Building,
    },
    {
        title: 'Activos',
        href: '/assets',
        icon: Monitor,
    },
    {
        title: 'Adquisiciones',
        href: '/procurements',
        icon: ShoppingCart,
    },
    // {
    //     title: 'Mantenimiento',
    //     href: '#', // TODO: maintenance route
    //     icon: Wrench,
    // },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup>
                <SidebarGroupLabel>General</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url === '/dashboard'" :tooltip="'Dashboard'" as-child>
                            <Link href="/dashboard">
                                <LayoutGrid />
                                <span>Dashboard</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup>
                <SidebarGroupLabel>Ubicaciones</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/cities')" :tooltip="'Ciudades'" as-child>
                            <Link href="/cities">
                                <MapPin />
                                <span>Ciudades</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/branches')" :tooltip="'Sucursales'" as-child>
                            <Link href="/branches">
                                <Building />
                                <span>Sucursales</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>

                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup>
                <SidebarGroupLabel>Gestión</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/users')" :tooltip="'Usuarios'" as-child>
                            <Link href="/users">
                                <Users />
                                <span>Usuarios</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/assets')" :tooltip="'Activos'" as-child>
                            <Link href="/assets">
                                <Monitor />
                                <span>Activos</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/procurements')" :tooltip="'Adquisiciones'" as-child>
                            <Link href="/procurements">
                                <ShoppingCart />
                                <span>Adquisiciones</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <SidebarGroup>
                <SidebarGroupLabel>Configuración</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/branch-types')" :tooltip="'Tipos de Sucursal'" as-child>
                            <Link href="/branch-types">
                                <Settings2 />
                                <span>Tipos de Sucursal</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                     <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/owners')" :tooltip="'Propietarios'" as-child>
                            <Link href="/owners">
                                <Building />
                                <span>Propietarios</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/job-titles')" :tooltip="'Cargos'" as-child>
                            <Link href="/job-titles">
                                <Users />
                                <span>Cargos</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/brands')" :tooltip="'Marcas'" as-child>
                            <Link href="/brands">
                                <Settings2 />
                                <span>Marcas</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                     <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/models')" :tooltip="'Modelos'" as-child>
                            <Link href="/models">
                                <Settings2 />
                                <span>Modelos</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/software-catalog')" :tooltip="'Software'" as-child>
                            <Link href="/software-catalog">
                                <Disc />
                                <span>Software</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/asset-types')" :tooltip="'Tipos de Activo'" as-child>
                            <Link href="/asset-types">
                                <Settings2 />
                                <span>Tipos de Activo</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/asset-status')" :tooltip="'Estados de Activo'" as-child>
                            <Link href="/asset-status">
                                <Activity />
                                <span>Estados de Activo</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/criticality')" :tooltip="'Niveles de Criticidad'" as-child>
                            <Link href="/criticality">
                                <ShieldAlert />
                                <span>Criticidad</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="default" :is-active="$page.url.startsWith('/configs')" :tooltip="'Ajustes del Sistema'" as-child>
                            <Link href="/configs">
                                <Settings2 />
                                <span>Ajustes del Sistema</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
