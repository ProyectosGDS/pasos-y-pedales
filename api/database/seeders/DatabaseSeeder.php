<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Documento;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Estado;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Expediente;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Sede;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Solicitud;
use App\Models\UnidadConvivenciaSocial\PasosPedales\TipoPersona;
use App\Models\UnidadConvivenciaSocial\PasosPedales\Workflow;
use App\Models\User;
use App\Models\UserInformation;
use App\Models\Zona;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Page::create([
            'label' => 'Admin',
            'icon' => 'building-shield',
            'type' => 'parent',
            'order' => 1,
        ]);

        Page::create([
            'label' => 'Users',
            'route' => 'Users',
            'icon' => 'users',
            'page_id' => 1,
            'type' => 'page',
            'order' => 1,
        ]);

        Page::create([
            'label' => 'Pages',
            'route' => 'Pages',
            'icon' => 'globe',
            'page_id' => 1,
            'type' => 'page',
            'order' => 2,
        ]);

        Page::create([
            'label' => 'Menus',
            'route' => 'Menus',
            'icon' => 'layer-group',
            'page_id' => 1,
            'type' => 'page',
            'order' => 3,
        ]);

        Page::create([
            'label' => 'Roles',
            'route' => 'Roles',
            'icon' => 'tag',
            'page_id' => 1,
            'type' => 'page',
            'order' => 4,
        ]);
        
        Page::create([
            'label' => 'Permissions',
            'route' => 'Permissions',
            'icon' => 'lock',
            'page_id' => 1,
            'type' => 'page',
            'order' => 5,
        ]);
        
        Page::create([
            'label' => 'Profiles',
            'route' => 'Profiles',
            'icon' => 'user-tag',
            'page_id' => 1,
            'type' => 'page',
            'order' => 6,
        ]);

        // PASOS Y PEDALES

        Page::create([
            'label' => 'Pasos y pedales',
            'icon' => 'bicycle',
            'type' => 'parent',
            'order' => 2,
        ]);

        Page::create([
            'label' => 'Recepción',
            'route' => 'Recepcion',
            'icon' => 'file-signature',
            'page_id' => 8,
            'type' => 'page',
            'order' => 1,
        ]);

        Page::create([
            'label' => 'Asignaciones',
            'route' => 'Asignaciones',
            'icon' => 'map-location-dot',
            'page_id' => 8,
            'type' => 'page',
            'order' => 2,
        ]);

        Page::create([
            'label' => 'Autorizaciones',
            'route' => 'Autorizaciones',
            'icon' => 'user-shield',
            'page_id' => 8,
            'type' => 'page',
            'order' => 3,
        ]);

        Page::create([
            'label' => 'Time line',
            'route' => 'Time line',
            'icon' => 'timeline',
            'page_id' => 8,
            'type' => 'page',
            'order' => 4,
        ]);

        Page::create([
            'label' => 'Solicitud',
            'route' => 'Solicitud',
            'icon' => 'bell-concierge',
            'page_id' => 8,
            'type' => 'page',
            'order' => 5,
        ]);

        $menu = Menu::create([
            'name' => 'Sysadmin'
        ]);

        $role = Role::create([
            'name' => 'Sysadmin'
        ]);

        Permission::create([
            'name' => 'view list users',
            'guard_name' => 'web',
            'module' => 'users'
        ]);
        Permission::create([
            'name' => 'store user',
            'guard_name' => 'web',
            'module' => 'users'
        ]);
        Permission::create([
            'name' => 'edit user',
            'guard_name' => 'web',
            'module' => 'users'
        ]);
        Permission::create([
            'name' => 'delete user',
            'guard_name' => 'web',
            'module' => 'users'
        ]);
        Permission::create([
            'name' => 'reset password user',
            'guard_name' => 'web',
            'module' => 'users'
        ]);
        Permission::create([
            'name' => 'view list pages',
            'guard_name' => 'web',
            'module' => 'pages'
        ]);
        Permission::create([
            'name' => 'store page',
            'guard_name' => 'web',
            'module' => 'pages'
        ]);
        Permission::create([
            'name' => 'edit page',
            'guard_name' => 'web',
            'module' => 'pages'
        ]);
        Permission::create([
            'name' => 'delete page',
            'guard_name' => 'web',
            'module' => 'pages'
        ]);
        Permission::create([
            'name' => 'view list menus',
            'guard_name' => 'web',
            'module' => 'menus'
        ]);
        Permission::create([
            'name' => 'store menu',
            'guard_name' => 'web',
            'module' => 'menus'
        ]);
        Permission::create([
            'name' => 'edit menu',
            'guard_name' => 'web',
            'module' => 'menus'
        ]);
        Permission::create([
            'name' => 'delete menu',
            'guard_name' => 'web',
            'module' => 'menus'
        ]);
        Permission::create([
            'name' => 'view list roles',
            'guard_name' => 'web',
            'module' => 'roles'
        ]);
        Permission::create([
            'name' => 'store role',
            'guard_name' => 'web',
            'module' => 'roles'
        ]);
        Permission::create([
            'name' => 'edit role',
            'guard_name' => 'web',
            'module' => 'roles'
        ]);
        Permission::create([
            'name' => 'delete role',
            'guard_name' => 'web',
            'module' => 'roles'
        ]);
        Permission::create([
            'name' => 'view list permissions',
            'guard_name' => 'web',
            'module' => 'permissions'
        ]);
        Permission::create([
            'name' => 'store permission',
            'guard_name' => 'web',
            'module' => 'permissions'
        ]);
        Permission::create([
            'name' => 'edit permission',
            'guard_name' => 'web',
            'module' => 'permissions'
        ]);
        Permission::create([
            'name' => 'delete permission',
            'guard_name' => 'web',
            'module' => 'permissions'
        ]);

        Permission::create([
            'name' => 'view list profiles',
            'guard_name' => 'web',
            'module' => 'profiles'
        ]);
        Permission::create([
            'name' => 'store profile',
            'guard_name' => 'web',
            'module' => 'profiles'
        ]);
        Permission::create([
            'name' => 'edit profile',
            'guard_name' => 'web',
            'module' => 'profiles'
        ]);
        Permission::create([
            'name' => 'delete profile',
            'guard_name' => 'web',
            'module' => 'profiles'
        ]);

        $role->syncPermissions(Permission::all()->pluck('id'));

        Profile::create([
            'name' => 'Sysadmin',
            'description' => 'lorem ipsmun all for get status for greate',
            'role_id' => 1,
            'menu_id' => 1,
        ]);

        User::create([
            'username' => '2733271000101',
            'password' => bcrypt('Cyb3rn3lsk8'),
            'profile_id' => 1,
        ]);


        UserInformation::create([
            'first_name' => 'Nelson',
            'last_name' => 'Vásquez',
            'cui' => '2733271000101',
            'phone' => '48840150',
            'birthday' => '1988-06-23',
            'city' => 'Guatemala',
            'address' => '2 calle 1-02 zona 3 anexo Ruedita',
            'email' => 'nelson.o.vasquez@gmail.com',
            'gender' => 'M',
            'user_id' => 1
        ]);

        $menu->pages()->sync(Page::all()->pluck('id'));


        for ($i=1; $i <= 25 ; $i++) { 
            Zona::create([
                'nombre' => 'Zona '.$i
            ]);
        }

        Sede::create([
            'nombre' => 'Avenidad Las Américas',
            'descripcion' => 'Aqui puede ir escrita una direccion o territorio como tambien cualquier tipo de descripcion',
        ]);

        Sede::create([
            'nombre' => 'Avenidad Simeón Cañas',
            'descripcion' => 'Aqui puede ir escrita una direccion o territorio como tambien cualquier tipo de descripcion',
        ]);

        Sede::create([
            'nombre' => 'Mariscal',
            'descripcion' => 'Aqui puede ir escrita una direccion o territorio como tambien cualquier tipo de descripcion',
        ]);

        TipoPersona::create([
            'nombre' => 'Individual'
        ]);

        TipoPersona::create([
           'nombre' => 'Juridica'
        ]);


        Estado::create([
            'nombre' => 'Solicitud ingresada',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 1,
        ]);

        Estado::create([
            'nombre' => 'Revición del expediente',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 2,
        ]);

        Estado::create([
            'nombre' => 'Solicitud aceptada',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 3,
        ]);

        Estado::create([
            'nombre' => 'Verificación de espacio',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 4,
        ]);

        Estado::create([
            'nombre' => 'Asignación de espacio',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 5,
        ]);

        Estado::create([
            'nombre' => 'Autorización de participación',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 6,
        ]);
        Estado::create([
            'nombre' => 'Rechazado',
            'descripcion' => 'Descripcion del estado',
            'tipo' => 'workflow',
            'orden' => 7,
        ]);

        
        Solicitud::create([
            'primer_nombre' => 'Fulano',
            'segundo_nombre' => 'Mengano',
            'primer_apellido' => 'Lopez',
            'segundo_apellido' => 'Acensio',
            'cui' => '1234567890123',
            'nit' => '123456789',
            'patente_comercio' => '123456789',
            'telefono' => '12345678',
            'correo' => 'example@example.com',
            'zona_id' => 3,
            'colonia' => 'Anexo Rudita',
            'domicilio' => '2 calle 1-02',
            'actividad_negocio' => 'Me dedico a vender chunches',
            'largo' => '3.5',
            'ancho' => '3.5',
            'observaciones' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Possimus optio ullam placeat assumenda dicta inventore rem iusto soluta non eveniet perspiciatis eius nostrum ratione nobis adipisci esse in, quae debitis!',
            'sede_id' => 1,
            'tipo_persona_id' => 1,
        ]);

        Documento::create([
            'nombre' => 'Carta de solicitud',
            'path' => 'UnidadConvivenciaSocial/PasosPedales/Solicitudes/0gr0LE8nKyBqr9qFzABn1s8b8JZ0Pjc2BMImNHhk.pdf',
            'solicitud_id' => 1,
        ]);
        Documento::create([
            'nombre' => 'Dpi',
            'path' => 'UnidadConvivenciaSocial/PasosPedales/Solicitudes/0gr0LE8nKyBqr9qFzABn1s8b8JZ0Pjc2BMImNHhk.pdf',
            'solicitud_id' => 1,
        ]);
        Documento::create([
            'nombre' => 'Rtu',
            'path' => 'UnidadConvivenciaSocial/PasosPedales/Solicitudes/0gr0LE8nKyBqr9qFzABn1s8b8JZ0Pjc2BMImNHhk.pdf',
            'solicitud_id' => 1,
        ]);
        Documento::create([
            'nombre' => 'Recibo de servicio',
            'path' => 'UnidadConvivenciaSocial/PasosPedales/Solicitudes/0gr0LE8nKyBqr9qFzABn1s8b8JZ0Pjc2BMImNHhk.pdf',
            'solicitud_id' => 1,
        ]);

        Documento::create([
            'nombre' => 'Patente de comercio',
            'path' => 'UnidadConvivenciaSocial/PasosPedales/Solicitudes/0gr0LE8nKyBqr9qFzABn1s8b8JZ0Pjc2BMImNHhk.pdf',
            'solicitud_id' => 1,
        ]);       
        

        Expediente::create([
            'solicitud_id' => 1,
        ]);

        Workflow::create([
            'expediente_id' => 1,
            'estado_id' => 1,
        ]);
               
    }
}
