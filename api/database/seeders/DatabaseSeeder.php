<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Page;
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
            'label' => 'Pasos y pedales',
            'icon' => 'circle',
            'type' => 'parent',
            'order' => 2,
        ]);

        Page::create([
            'label' => 'Recepción',
            'route' => 'Recepcion',
            'icon' => 'folder',
            'page_id' => 4,
            'type' => 'page',
            'order' => 1,
        ]);

        Page::create([
            'label' => 'Asignaciones',
            'route' => 'Asignaciones',
            'icon' => 'folder',
            'page_id' => 4,
            'type' => 'page',
            'order' => 2,
        ]);

        Page::create([
            'label' => 'Autorizaciones',
            'route' => 'Autorizaciones',
            'icon' => 'folder',
            'page_id' => 4,
            'type' => 'page',
            'order' => 3,
        ]);

        Menu::create([
            'name' => 'Sysadmin'
        ]);

        Role::create([
            'name' => 'Sysadmin'
        ]);

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

        $menu = Menu::find(1);
        $menu->pages()->sync([1,2,3,4,5,6,7]);


        for ($i=1; $i <= 25 ; $i++) { 
            Zona::create([
                'nombre' => 'zona '.$i
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
            'path' => 'analisis de conducta Skinner.pdf',
            'solicitud_id' => 1,
        ]);
        Documento::create([
            'nombre' => 'Dpi',
            'path' => 'Cv.pdf',
            'solicitud_id' => 1,
        ]);
        Documento::create([
            'nombre' => 'Rtu',
            'path' => 'Dilemas éticos y profesionales relacionados con las pruebas.pdf',
            'solicitud_id' => 1,
        ]);
        Documento::create([
            'nombre' => 'Recibo de servicio',
            'path' => 'hoja mumero dos.pdf',
            'solicitud_id' => 1,
        ]);

        Documento::create([
            'nombre' => 'Patente de comercio',
            'path' => 'KELIN.pdf',
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
