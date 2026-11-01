<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'shop-manager')->where('guard_name', 'web')->firstOrFail();

        $shops = [
            // DHAKA
            ['tienda' => 'ENTREVIAS',                   'email' => 'ENTREVIAS@dhakafrutas.com',                   'address' => 'CALLE VIZCONDE DE ARLESSON-05',       'empresa' => 'DHAKA'],
            ['tienda' => 'LAGUNA -91',                  'email' => 'LAGUNA-91@dhakafrutas.com',                   'address' => 'CALLE LAGUNA-91',                     'empresa' => 'DHAKA'],
            ['tienda' => 'VALLECAS',                    'email' => 'VALLECAS@dhakafrutas.com',                    'address' => 'CALLE SIERRA MORENA-27',              'empresa' => 'DHAKA'],
            ['tienda' => 'CALLE ALTOS DE SACERULA - 2', 'email' => 'CALLEALTOSDESACERULA-2@dhakafrutas.com',     'address' => 'CALLE ALTOS DE SECURALA-2',           'empresa' => 'DHAKA'],
            ['tienda' => 'CALLE FUENLABRADA - 13',      'email' => 'CALLEFUENLABRADA-13@dhakafrutas.com',        'address' => 'CALLE FUENLABRADA-13',                'empresa' => 'DHAKA'],
            ['tienda' => 'NUEVA - 09',                  'email' => 'NUEVA-09@dhakafrutas.com',                    'address' => 'CALLE NUEVA-09',                      'empresa' => 'DHAKA'],
            ['tienda' => 'CARABANCHEL - 15',            'email' => 'CARABANCHEL-15@dhakafrutas.com',              'address' => 'CALLE EL ESPINAR-16',                 'empresa' => 'DHAKA'],
            ['tienda' => 'CARABANCHEL - 19',            'email' => 'CARABANCHEL-19@dhakafrutas.com',              'address' => 'AVENIDA NUESTRA SEÑORA DE FATIMA-21', 'empresa' => 'DHAKA'],
            ['tienda' => 'TORREJON',                    'email' => 'TORREJON@dhakafrutas.com',                    'address' => 'CALLE SAN ISIDRO-07',                 'empresa' => 'DHAKA'],
            ['tienda' => 'USERA',                       'email' => 'USERA@dhakafrutas.com',                       'address' => 'CALLE MANUEL NOYA-10',                'empresa' => 'DHAKA'],
            ['tienda' => 'LAGUNA-97',                   'email' => 'LAGUNA-97@dhakafrutas.com',                   'address' => 'CALLE LAGUNA-97',                     'empresa' => 'DHAKA'],

            // FRESCAS
            ['tienda' => 'BUENAVISTA -12',              'email' => 'BUENAVISTA-12@dhakafrutas.com',               'address' => 'CALLE BUENAVISTA-12',                 'empresa' => 'FRESCAS'],
            ['tienda' => 'EXTREMADURA',                 'email' => 'EXTREMADURA@dhakafrutas.com',                 'address' => 'AVD PORTUGAL-163',                    'empresa' => 'FRESCAS'],
            ['tienda' => 'CAMINO VIEJO',                'email' => 'CAMINOVIEJO@dhakafrutas.com',                 'address' => 'CAMINO VIEJO DE LEGANES-153',         'empresa' => 'FRESCAS'],
            ['tienda' => 'VILLAVICIOSA-34',             'email' => 'VILLAVICIOSA-34@dhakafrutas.com',             'address' => 'CALLE VILLAVICIOSA-34',               'empresa' => 'FRESCAS'],
            ['tienda' => 'COLONIA JARDIN OLD',          'email' => 'COLONIAJARDINOLD@dhakafrutas.com',            'address' => 'CALLE VILLACARRIEDO-22',              'empresa' => 'FRESCAS'],
            ['tienda' => 'CALLE OCA - 75',              'email' => 'CALLEOCA-75@dhakafrutas.com',                 'address' => 'CALLE OCA-75',                        'empresa' => 'FRESCAS'],
            ['tienda' => 'VITAL AZA-84',                'email' => 'VITALAZA-84@dhakafrutas.com',                 'address' => 'CALLE VITAL AZA-84',                  'empresa' => 'FRESCAS'],
            ['tienda' => 'TETUAN',                      'email' => 'TETUAN@dhakafrutas.com',                      'address' => 'CALLE BRAVO MORILLO-248',             'empresa' => 'FRESCAS'],
            ['tienda' => 'HALCON',                      'email' => 'HALCON@dhakafrutas.com',                      'address' => 'CALLE HALCON-43',                     'empresa' => 'FRESCAS'],

            // TAFSIN
            ['tienda' => 'SIERRA DE CONTRAVIESA - 22',  'email' => 'SIERRADECONTRAVIESA-22@dhakafrutas.com',      'address' => 'CALLE SIERRA DE CONTRAVISA-22',       'empresa' => 'TAFSIN'],
            ['tienda' => 'ALCALA DIA',                  'email' => 'ALCALADIA@dhakafrutas.com',                   'address' => 'CALLE MARQUES ALONSO MARTINEZ-7',     'empresa' => 'TAFSIN'],
            ['tienda' => 'ALCALA CARREFOUR',            'email' => 'ALCALACARREFOUR@dhakafrutas.com',             'address' => 'CALLE VIA COMPLUTENSE-42',            'empresa' => 'TAFSIN'],
            ['tienda' => 'GETAFE CARNESERIA - 16',      'email' => 'GETAFECARNESERIA-16@dhakafrutas.com',         'address' => 'AVENIDA CIUDADES-16',                 'empresa' => 'TAFSIN'],
            ['tienda' => 'VALDEACEDERAS',               'email' => 'VALDEACEDERAS@dhakafrutas.com',               'address' => 'CALLE CAPITAN BLANCO ARGIBAY-29',     'empresa' => 'TAFSIN'],
            ['tienda' => 'FRANCOS RODRIGUEZ -49',       'email' => 'FRANCOSRODRIGUEZ-49@dhakafrutas.com',         'address' => 'CALLE FRANCOS RODRIGUEZ-49',          'empresa' => 'TAFSIN'],
            ['tienda' => 'GETAFE CONSTITUCION',         'email' => 'GETAFECONSTITUCION@dhakafrutas.com',          'address' => 'PLAZA CONSTITUCION-05,GETAFE',        'empresa' => 'TAFSIN'],
            ['tienda' => 'PROSPERIDAD',                 'email' => 'PROSPERIDAD@dhakafrutas.com',                 'address' => 'AVENIDA LUIS VIVES-14',               'empresa' => 'TAFSIN'],
            ['tienda' => 'GETAFE PALACIOS',             'email' => 'GETAFEPALACIOS@dhakafrutas.com',              'address' => 'PLAZA GENERAL PALACIOS-6,GETAFE',     'empresa' => 'TAFSIN'],
            ['tienda' => 'OPORTO NEW',                  'email' => 'OPORTONEW@dhakafrutas.com',                   'address' => 'CALLE VALLE DE ORO-29',               'empresa' => 'TAFSIN'],
            ['tienda' => 'FRANCISCO PAINO - 39',        'email' => 'FRANCISCOPAINO-39@dhakafrutas.com',           'address' => 'CALLE FRANCISCO PAINO-39',            'empresa' => 'TAFSIN'],
            ['tienda' => 'LUCANO',                      'email' => 'LUCANO@dhakafrutas.com',                      'address' => 'CALLE AQULIES-25',                    'empresa' => 'TAFSIN'],
            ['tienda' => 'ETRURIA-6',                   'email' => 'ETRURIA-6@dhakafrutas.com',                   'address' => 'CALLE ETRURIA-06',                    'empresa' => 'TAFSIN'],
            ['tienda' => 'ALCALA NEW',                  'email' => 'ALCALANEW@dhakafrutas.com',                   'address' => 'AVENIDA CABALLERIA ESPAÑOLA-10',      'empresa' => 'TAFSIN'],
            ['tienda' => 'ARAGON-05',                   'email' => 'ARAGON-05@dhakafrutas.com',                   'address' => 'AVENIDA ARAGON-05',                   'empresa' => 'TAFSIN'],
            ['tienda' => 'CALLE MURCIA - 17',           'email' => 'CALLEMURCIA-17@dhakafrutas.com',              'address' => 'CALLE MURCIA-17',                     'empresa' => 'TAFSIN'],
            ['tienda' => 'AL BUFERA-97',                'email' => 'ALBUFERA-97@dhakafrutas.com',                 'address' => 'AVENIDA AL BUFERA-97',                'empresa' => 'TAFSIN'],
            ['tienda' => 'PARLA - 81',                  'email' => 'PARLA-81@dhakafrutas.com',                    'address' => 'CALLE REAL 81,PARLA',                 'empresa' => 'TAFSIN'],

            // SOHANA
            ['tienda' => 'PACIFICO',                    'email' => 'PACIFICO@dhakafrutas.com',                    'address' => 'SANCHEZ BARCAIZTEGUI-20',             'empresa' => 'SOHANA'],
            ['tienda' => 'ESTRECHO',                    'email' => 'ESTRECHO@dhakafrutas.com',                    'address' => 'CALLE BRAVO MORILLO-198',             'empresa' => 'SOHANA'],
            ['tienda' => 'ALVARADO',                    'email' => 'ALVARADO@dhakafrutas.com',                    'address' => 'CALLE BRAVO MORILLO-130',             'empresa' => 'SOHANA'],
            ['tienda' => 'MERCADONA',                   'email' => 'MERCADONA@dhakafrutas.com',                   'address' => 'CALLE MARTINEZ DE LA RIVA-4',         'empresa' => 'SOHANA'],
            ['tienda' => 'PUENTE DE VALLECAS',          'email' => 'PUENTEDEVALLECAS@dhakafrutas.com',            'address' => 'CALLE MOANTE IGUELDO 26',             'empresa' => 'SOHANA'],
            ['tienda' => 'CIMPOZULEOS',                 'email' => 'CIMPOZULEOS@dhakafrutas.com',                 'address' => 'CALL JERONIMO DEL MORAL-15',          'empresa' => 'SOHANA'],

            // NARSINGDI
            ['tienda' => 'MOSTOLES PEQ',                'email' => 'MOSTOLESPEQ@dhakafrutas.com',                 'address' => 'CALLE CONCORDIA-02,MOSTOLES',         'empresa' => 'NARSINGDI'],
            ['tienda' => 'LUSERO',                      'email' => 'LUSERO@dhakafrutas.com',                      'address' => 'CALLE JARAIZ DE LA VERA-02',          'empresa' => 'NARSINGDI'],
            ['tienda' => 'DELECIAS',                    'email' => 'DELECIAS@dhakafrutas.com',                    'address' => 'PASEO DELECIAS-78',                   'empresa' => 'NARSINGDI'],
            ['tienda' => 'PUERTA DE ANGEL-01',          'email' => 'PUERTADEANGEL-01@dhakafrutas.com',            'address' => 'CALLE CARAMUEL-7',                    'empresa' => 'NARSINGDI'],
            ['tienda' => 'LOS YEBENES',                 'email' => 'LOSYEBENES@dhakafrutas.com',                  'address' => 'CALLE LOS LLEVENES-84',               'empresa' => 'NARSINGDI'],
            ['tienda' => 'VILLAVERDE ALTO',             'email' => 'VILLAVERDEALTO@dhakafrutas.com',              'address' => 'CALLE ALBINO HERNANDEZ LAZARO',       'empresa' => 'NARSINGDI'],
            ['tienda' => 'VALVANERA-88',                'email' => 'VALVANERA-88@dhakafrutas.com',                'address' => 'CALLE VALVANERA-88',                  'empresa' => 'NARSINGDI'],
            ['tienda' => 'VICAL VARO',                  'email' => 'VICALVARO@dhakafrutas.com',                   'address' => 'CALLE CONDESA VEGADEL POZO',          'empresa' => 'NARSINGDI'],
            ['tienda' => 'MOSTOLES GRANDE',             'email' => 'MOSTOLESGRANDE@dhakafrutas.com',              'address' => 'PASEO GOYA-07,MOSTOLES',              'empresa' => 'NARSINGDI'],
            ['tienda' => 'MOSTOLES NEW',                'email' => 'MOSTOLESNEW@dhakafrutas.com',                 'address' => 'AVENIDA FORTUNY-05,MOSTOLES',         'empresa' => 'NARSINGDI'],
            ['tienda' => 'TOLEDO-35',                   'email' => 'TOLEDO-35@dhakafrutas.com',                   'address' => 'CALLE TOLEDO-35',                     'empresa' => 'NARSINGDI'],
            ['tienda' => 'PUERTA DE ANGEL-02',          'email' => 'PUERTADEANGEL-02@dhakafrutas.com',            'address' => 'CALLE DE BADAJOZ',                    'empresa' => 'NARSINGDI'],
            ['tienda' => 'MOSTOLES NEW 2',              'email' => 'MOSTOLESNEW2@dhakafrutas.com',                'address' => 'CALLE DE BADAJOZ',                    'empresa' => 'NARSINGDI'],
            ['tienda' => 'NUEVA NUMANCIA',              'email' => 'NUEVANUMANCIA@dhakafrutas.com',               'address' => 'CALLE PUERTO DEL MONASTERIO-05',      'empresa' => 'NARSINGDI'],

            // DEL CAMPO
            ['tienda' => 'PARLA ALI - 1',               'email' => 'PARLAALI-1@dhakafrutas.com',                  'address' => 'CALLE REAL- 84,PARLA',                'empresa' => 'DEL CAMPO'],
            ['tienda' => 'PARLA ALI - 2',               'email' => 'PARLAALI-2@dhakafrutas.com',                  'address' => 'CALLE SORIA-2,PARLA',                 'empresa' => 'DEL CAMPO'],
            ['tienda' => 'GATEFE GRANDE',               'email' => 'GATEFEGRANDE@dhakafrutas.com',                'address' => 'AVENIDA JAZMIN-04,GETAFE',            'empresa' => 'DEL CAMPO'],
            ['tienda' => 'GETAFE PEQ',                  'email' => 'GETAFEPEQ@dhakafrutas.com',                   'address' => 'CALLE CATALUÑA-17,GETAFE',            'empresa' => 'DEL CAMPO'],

            // TAMJID
            ['tienda' => 'ALCALA GUADELAJARA',          'email' => 'ALCALAGUADELAJARA@dhakafrutas.com',           'address' => 'AV. GUADELAJARA 07',                  'empresa' => 'TAMJID'],
            ['tienda' => 'ALBERTO PALACIOS-42',         'email' => 'ALBERTOPALACIOS-42@dhakafrutas.com',          'address' => 'PASEO ALBERTO PALACIOS-42',           'empresa' => 'TAMJID'],
            ['tienda' => 'MONSENOR OSCAR',              'email' => 'MONSENOROSCAR@dhakafrutas.com',               'address' => 'C. de Monseñor Oscar Romero, 2',      'empresa' => 'TAMJID'],

            // YASIN
            ['tienda' => 'PUENTE DE VALLECAS',          'email' => 'PUENTEDEVALLECAS2@dhakafrutas.com',           'address' => 'CALLE ALBUFERA 23',                   'empresa' => 'YASIN'],
            ['tienda' => 'TOLEDO 35',                   'email' => 'TOLEDO35@dhakafrutas.com',                    'address' => 'CALLE LEONCIO ROJAS GETAFE',          'empresa' => 'YASIN'],

            // ANWAR
            ['tienda' => 'VALLECAS OLD',                'email' => 'VALLECASOLD@dhakafrutas.com',                 'address' => 'CALLE SIERRA MORENA 27',              'empresa' => 'ANWAR'],

            // RICHI FRUTAS
            ['tienda' => 'PARLA LITON BHAI',            'email' => 'PARLALITONBHAI@dhakafrutas.com',              'address' => 'CALLE REAL 69 PARLA',                 'empresa' => 'RICHI FRUTAS'],

            // ATIKUR FRUTAS
            ['tienda' => 'IGLESIA ATIK BHAI',           'email' => 'IGLESIAATIKBHAI@dhakafrutas.com',             'address' => 'MARQUESA VIUDA DE ALDAMA ALCOBINDAS', 'empresa' => 'ATIKUR FRUTAS'],

            // FRUTAS ISHRAK
            ['tienda' => 'SOHEL BHAI 01',               'email' => 'SOHELBHAI01@dhakafrutas.com',                 'address' => 'CALLE ALCALA 441',                    'empresa' => 'FRUTAS ISHRAK'],
            ['tienda' => 'SOHEL BHAI 02',               'email' => 'SOHELBHAI02@dhakafrutas.com',                 'address' => 'CALLE EZEQUIEL SOLANA 83',            'empresa' => 'FRUTAS ISHRAK'],

            // STAR FRUTAS
            ['tienda' => 'SHOPAN BHAI',                 'email' => 'SHOPANBHAI@dhakafrutas.com',                  'address' => 'CALLE DE CAPITAN BLANCO ARGIBAY 61',  'empresa' => 'STAR FRUTAS'],

            // NUSIBA FRUTAS
            ['tienda' => 'ALCALA 280',                  'email' => 'ALCALA280@dhakafrutas.com',                   'address' => 'CALLE ALCALA 280 CDAD LINEAL',        'empresa' => 'NUSIBA FRUTAS'],
            ['tienda' => 'ALCALA 390',                  'email' => 'ALCALA390@dhakafrutas.com',                   'address' => 'CALLE ALCALA 290 CDAD LINEAL',        'empresa' => 'NUSIBA FRUTAS'],
            ['tienda' => 'HERMANO GARCIA',              'email' => 'HERMANOGARCIA@dhakafrutas.com',               'address' => 'GARCIA NOBLEJAS',                     'empresa' => 'NUSIBA FRUTAS'],

            // MAA FRUTAS
            ['tienda' => 'RASEL BHAI',                  'email' => 'RASELBHAI@dhakafrutas.com',                   'address' => 'CALLE AVENIDA DE FUENLABRADA 103',    'empresa' => 'MAA FRUTAS'],

            // LAS 4 ROSAS
            ['tienda' => 'KASHEM BHAI',                 'email' => 'KASHEMBHAI@dhakafrutas.com',                  'address' => 'CALLE MORANA 25 LEGANES',             'empresa' => 'LAS 4 ROSAS'],
        ];

        foreach ($shops as $index => $shop) {
            // Code = cleaned TIENDA name
            $code = strtoupper(trim($shop['tienda']));
            $code = preg_replace('/\s+/', '-', $code);          // space → -
            $code = preg_replace('/[^A-Z0-9\-]/', '', $code);   // remove special chars
            $code = preg_replace('/-+/', '-', $code);           // multiple - → single
            $code = trim($code, '-');

            // Handle duplicate "PUENTE DE VALLECAS"
            if ($code === 'PUENTE-DE-VALLECAS' && $shop['empresa'] === 'YASIN') {
                $code = 'PUENTE-DE-VALLECAS-2';
            }

            $email = strtolower(str_replace(' ', '', $shop['email']));

            DB::table('shops')->updateOrInsert(
                ['code' => $code],
                [
                    'name'         => $shop['tienda'] . ' - ' . $shop['empresa'],
                    'manager_name' => $shop['empresa'] . ' Manager',
                    'phone'        => '+34 910 000 ' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'email'        => $email,
                    'address'      => $shop['address'],
                    'city'         => 'Madrid',
                    'status'       => 'active',
                    'deleted_at'   => null,
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ]
            );

            $shopId = DB::table('shops')->where('code', $code)->value('id');

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'     => $shop['empresa'] . ' Manager',
                    'password' => bcrypt('12345678'),
                    'shop_id'  => $shopId,
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}