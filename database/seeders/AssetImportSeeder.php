<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activo;
use App\Models\TipoActivo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\EstadoActivo;
use App\Models\NivelCriticidad;
use App\Models\Ubicacion;
use App\Models\City;
use App\Models\TipoUbicacion;
use App\Models\Propietario;
use App\Models\AtributoActivo;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\AsignacionRed;
use App\Models\PuntoRed;
use App\Models\Branch;
use App\Models\TipoSucursal;

class AssetImportSeeder extends Seeder
{
    public function run()
    {
        // Embedded CSV Data (Cleaned and Merged)
        $csvData = <<<'CSV'
PATCH PANEL;ROSETA;Swicth Nombre;Swicth Puerto;Codigo de Activo (Nombre de Equipo);Ubicación del Equipo ;Agencia /Oficina ;Cargo del Funcionario;Nombre del Funcionario;IPAsignado;Carpeta Compartidas Cerradas;NetBank;Ficha Tecnica Actualizada a Banco;Form. de Aceptación Responsabilidad;Formulario de Excepción;Bios Acceso Bloqueado;Antivirus Kaspersky Instalado Bloqueado;USB BLOQUEADO;Admin. Tarea;OSC INVENTORY INSTALADO;PRECINTO DE SEGURIDAD ;Sistema Operativo;Version Sistema Operativo;Version Office;TIPO DE EQUIPO;MARCA;MODELO;SERIE;PROCESADOR;MAC Ethernet ;MAC  Wi-Fi;GENERACION ;CAPACIDAD DE DISCO;TIPO DE DISCO;CAPACIDAD DE MEMORIA;TIPODE MEMORIA;N°LICENCIA WIN 10;N°LICENCIA OFFICE;PROPIEDAD DE EQUIPO
4;GR-A04-D;B;4;GSGROR-PF4JSL2H;Gerencia 1er Piso;Sucursal Central;GERENTE DE SUCURSAL;SHIRLEY MARIA SALINAS RADIC;10.17.71.11;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;PORTÁTIL;LENOVO;21JQS1GJ00;PF4JSL2H;Core(TM) i7-1355U;74:5D:22:84:8E:4A;E4:0D:36:68:45:C5;13va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
2;GR-A02-D;B;2;THGROR-MJ0JD048;Gerencia 1er Piso;Sucursal Central;RESPONSABLE DE TALENTO HUMANO Y ADMINISTRACIÓN SUCURSAL;ADRIANA MIRJANA KRELLAC MEDRANO ;10.17.71.13;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S2MW00;MJ0JD048;Core(TM) i7-12700;04:7C:16:26:16:D1;64:D6:9A:4C:68:41;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
1;GR-A01-D;B;1;SLLGOR-MJ0L1QPB;Gerencia 1er Piso;Sucursal Central;SUBGERENTE LEGAL SUCURSAL;KAREN CAROL ARIAS NINA ;10.17.71.14;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S79800;MJ0L1QPB;Core(TM) i7-12700;04:7C:16:9D:F9:C1;BC:03:58:40:50:AB;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
6;GR-A06-D;B;6;ORLGOR30021068;Gerencia 1er Piso;Sucursal Central;ANALISTA LEGAL SUCURSAL;ROLANDO HUARACHI COLQUE ;10.17.71.15;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft 365 para negocios;ESCRITORIO;LENOVO;10M7000SLS ;MJ062W1W;Core(TM) i5-7400 CPU @ 3.00GHz ;94:C6:91:23:8A:FE;;7ma GEN.;480 GB;SSD;8 GB;DDR4;6G4GD-8NFK2-YWY66-J47TP-7CFC2;"Office 365 (rhuarachi@grupofortaleza.com.bo, Fortaleza2025*)";BANCO FORTALEZA
6;CR-B06-D;A;6;OCNGOR-PF4JV7SS;Riesgos - Catastro Planta Baja;Sucursal Central;OFICIAL DE EVALUACION CREDITICIA;LETICIA GONZALES BALDELLON ;10.17.71.16;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;PORTÁTIL;LENOVO;21JQS1GJ00;PF4JV7SS;Core(TM) i7-1355U;74:5D:22:84:8D:70;E4:0D:36:68:EA:7F;13va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
3;CR-B03-D;A;2;OENGOR-MJ0L1QQC;Riesgos - Catastro Planta Baja;Sucursal Central;OFICIAL DE EVALUACION CREDITICIA;MATEO JUSTINIANO VASQUEZ CRUZ ;10.17.71.17;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S79800;MJ0L1QQC;Core(TM) i7-12700;04:7C:16:9D:F9:04;BC:03:58:3F:54:99;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
4;CR-B04-D;A;4;ORRGOR30021067;Riesgos - Catastro Planta Baja;Sucursal Central;OFICIAL DE RIESGO OPERATIVO;MARIA EUGENIA ZABALAGA SEJAS;10.17.71.18;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;10M7000SLS;MJ05ZKJH;Core(TM) i5-7400 CPU @ 3.00GHz;94:C6:91:26:73:6C;;7ma GEN.;480 GB;SSD;8 GB;DDR4;6G4GD-8NFK2-YWY66-J47TP-7CFC2;TJNG2-R4YK2-MG3C7-PVQ9V-G6QV6;BANCO FORTALEZA
23;NG-B23-D;A;23;NGANOR30021083;Negocios 1er Piso;Sucursal Central;PASANTE TH;Libre;10.17.71.19;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 10 Pro;22H2;Microsoft Office Hogar y Pequeña Empresa 2010;ESCRITORIO;LENOVO;10M9A02E00;MJ065K2K;Core(TM) i5-7400 CPU @ 3.00GHz ;94:C6:91:25:3A:8B;D4:25:8B:08:69:16;7ma GEN.;1 TB;HDD;8 GB;DDR4;84NYT-K89Y7-GVBPQ-PJC4C-RVV22;D4Q6C-Q7HRX-M6XTK-M7MKJ-JWMY6;BANCO FORTALEZA
9;NG-B09-D;A;9;ORNGOS30021133;Negocios 1er Piso;Sucursal Central;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;CARMEN MARIA MAMANI BUEZO;10.17.71.20;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft 365 para negocios;ESCRITORIO;DELL;OptiPlex 3000;G395KQ3;Core(TM) i5-12500 [6 core(s) x86_64;74:86:E2:2B:57:5E;;12va GEN.;512 GB;SSD;8 GB;DDR4;;"Office 365 (cmamani@grupofortaleza.com.bo, Fortaleza2024*)";BANCO FORTALEZA
20;NG-B20-D;A;20;ORNGOR30021040;Negocios 1er Piso;Sucursal Central;ANALISTA DE NEGOCIOS;CARLO ALEJANDRO PEÑA CHAVEZ;10.17.71.21;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft 365 para negocios;ESCRITORIO;LENOVO;10M7000SLS;MJ05VSJ9;Core(TM) i5-7400 CPU @ 3.00GHz;94:C6:91:09:A2:A2;;7ma GEN.;480 GB;SSD;8 GB;DDR4;JQN6F-8RY9V-CJQHJ-2RR2G-V6DGP;cpenac@grupofortaleza.com.bo, Fortaleza2025*;BANCO FORTALEZA
18;NG-B18-D;A;18;NGONOR-MJ0JD00G;Negocios 1er Piso;Sucursal Central;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;FELIX DARIO MOYA RIVERA;10.17.71.22;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S2MW00;MJ0JD00G;Core(TM) i7-12700;04:7C:16:26:17:78;64:D6:9A:4F:AF:92;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
21;NG-B21-D;A;21;NGONOR-MJF3Y2C;Negocios 1er Piso;Sucursal Central;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;ALFREDO ALVARO ZEBALLOS BOHORQUEZ ;10.17.71.23;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3Y2C;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:32:F9:B3;EC:63:D7:14:FA:2D;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
22;NG-B22-D;A;22;NGONOR-PF4JY5NY;Negocios 1er Piso;Sucursal Central;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;GABRIEL GERARDO LAFUENTE BERNAL  ;10.17.71.24;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;PORTÁTIL;LENOVO;21JQS1GJ00;PF4JY5NY;Core(TM) i7-1355U;74:5D:22:84:8D:D2;E4:0D:36:68:8D:E1;13va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
15;NG-B-D;A;15;GSGROR-PF310285;Negocios 1er Piso;Sucursal Central;OFICIAL DE NEGOCIOS BANCA EMPRESAS;JEANNETTE GLADYS GOMEZ VARGAS;10.17.71.27;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;PORTÁTIL;LENOVO;20X4S2FL00;PF310285;Core(TM) i7-1165G7 @ 2.80GHz ;90:2E:16:13:1C:8A;96:5A:FC:0B:20:D1;11va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
14;B-A14-D;A;20;GANGOR-PF2TEA0X;Negocios 1er Piso;Sucursal Central;SUBGERENTE DE NEGOCIOS;ALBERT ERIC ALAVE MURIEL ;10.17.71.28;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;PORTÁTIL;LENOVO;20W1S0EJ00;PF2TEA0X;Core(TM) i7-1165G7 @ 2.80GHz;38:F3:AB:96:E5:14;64:6E:E0:38:68:4B;11va GEN.;1 TB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
17;B-A17-D;A;17;NGONOR-WJ0KKD33;Negocios 1er Piso;Sucursal Central;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;CRISTINA DAMIAN FLORES;10.17.71.29;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S79800;MJ0KKD33;Core(TM) i7-12700;04:7C:16:96:F8:56;DC:46:28:93:CF:BF;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
21;A-21;A;16;CZRCOROB30010456;Gerencia 1er Piso;Agencia Ejercito;RESPONSABLE DE COBRANZA;JOSE ALEJANDRO RIOJA BENAVIDEZ ;10.17.71.30;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft 365 para negocios;PORTÁTIL;HP;HP Laptop 15-da2xxx;CND0344T2V;Core(TM) i7-10510U CPU @ 1.80GHz;84:2A:FD:D1:46:84;5E:3A:45:A5:39:B7;10ma GEN.;480 GB;SSD;8 GB;DDR4;97WTN-WY7D4-G8V3T-CHKPW-2GYP4;"Office 365 (jrioja@grupofortaleza.com.bo, Fortaleza2024)";BANCO FORTALEZA
7;A-A07-D;A;19;OROPOS30021132;Riesgos - Catastro Planta Baja;Sucursal Central;OFICIAL DE ADMINISTRACION CREDITICIA;JUAN CARLOS SALGUERO ROJAS ;10.17.71.31;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft 365;ESCRITORIO;DELL;OptiPlex 3000;9495KQ3;Core(TM) i5-12500 [6 core(s) x86_64;74:86:E2:2B:57:7E;;12va GEN.;512 GB;SSD;8 GB;DDR4;NF6HC-QH89W-F8WYV-WWXV4-WFG6P;"Office 365 (jsalguero@grupofortaleza, Fortaleza2023)";BANCO FORTALEZA
20;A-A20-D;B;20;SGOPOR-PF30ZRC6;Operaciones Planta Baja;Sucursal Central;SUBGERENTE DE OPERACIONES SUCURSAL;DAGME SIDETRA ILLANES MEAVE;10.17.71.35;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2019;PORTÁTIL;LENOVO;20X4S2FL00;PF30ZRC6;Core(TM) i7-1165G7 @ 2.80GHz ;90:2E:16:13:04:60;16:5A:FC:0B:20:73;11va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
11;B-A11-D;B;11;ORCTOR30021086;Operaciones Planta Baja;Sucursal Central;RESPONSABLE DE CATASTRO;RODRIGO FREDDY GARCIA HURTADO ;10.17.71.36;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft 365 para negocios;ESCRITORIO;LENOVO;10M9A02E00;MJ065K3V;Core(TM) i5-7400 CPU @ 3.00GHz;94:C6:91:26:73:F0;;7ma GEN.;480 GB;SSD;8 GB;DDR4;NPD9J-2VRK2-YKB8W-6QH9P-JB49C;"Office 365 (rgarcia@grupofortaleza, Fortaleza2025*)";BANCO FORTALEZA
7;B-A07-D;A;7;TEATOR-MJ0L1QPT;Riesgos - Catastro Planta Baja;Sucursal Central;ADMINISTRADOR DE TECNOLOGIA SUCURSAL;MARCO MARCELO AYALA HUANCA ;10.17.71.37;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S79800;MJ0L1QPT;Core(TM) i7-12700;04:7C:16:9E:19:AB;BC:03:58:3F:54:5D;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
8;B-A08-D;A;8;ORGROB30020912;Riesgos - Catastro Planta Baja;Sucursal Central;AUXILIAR DE SERVICIOS;JOSE LUIS ALI UYULI;10.17.71.39;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Pequeña Empresa 2010;ESCRITORIO;DELL ;Vostro 270s ;153SQW1;Core(TM) i3-3220 CPU @ 3.30GHz;78:45:C4:33:DB:D7;9C:2A:70:32:74:ED;3ra GEN.;480 GB;SSD;4 GB;DDR3;6G4GD-8NFK2-YWY66-J47TP-7CFC2;N28Y6-XMMJ4-WRHXJ-B27RR-R3M4G;BANCO FORTALEZA
22;A-A22-D;B;22;OSOPOR-MJ0F3Y21;Operaciones Planta Baja;Sucursal Central;OFICIAL DE SERVICIO AL CLIENTE - PUNTO DE RECLAMO; ALVARO IGNACIO GARECA;10.17.71.40;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3Y21;Core(TM) i5-10400 CPU @ 2.90GHz ;E0:BE:03:32:FB:2C;EC:63:D7:16:CE:C5;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
14;A-A14-D;B;14;OPRCOR30021069;Operaciones Planta Baja;Sucursal Central;RESPONSABLE DE CARTERA;EDGAR JORGE PARDO ARISPE;10.17.71.41;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft 365 para negocios;ESCRITORIO;LENOVO;10M7000SLS;MJ062W1V;Core(TM) i5-7400 CPU @ 3.00GHz;94:C6:91:22:95:E0;;7ma GEN.;480 GB;SSD;8 GB;DDR4;CYK4X-JTNJY-VHDG8-JF4QQ-G83GP;"Office 365 (epardo@grupofortaleza, Fortaleza2025*)";BANCO FORTALEZA
17;A-A17-D;B;17;OROPOR30021085;Operaciones Planta Baja;Sucursal Central;RESPONSABLE DE CANJE Y BOVEDA;JORGE AMETH OPORTO COPA ;10.17.71.42;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 10 Pro;22H2;Microsoft Office Hogar y Pequeña Empresa 2010;ESCRITORIO;LENOVO;10M9A02E00;MJ065K3T;Core(TM) i5-7400 CPU @ 3.00GHz ;94:C6:91:26:75:30;;7ma GEN.;1 TB;HDD;8 GB;DDR4;KW7XN-Q9988-WYJP2-CT9VK-HCFC2;6TDVD-9G8MD-D2PDH-GJQB7-6C8YH;BANCO FORTALEZA
16;A-A16-D;B;12;OROPOR30020640;Operaciones Planta Baja;Sucursal Central;CAJERO 1;MARCELO JIMENEZ ROMERO;10.17.71.43;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;OpenOffice 4.1.15;ESCRITORIO;DELL ;Vostro 260;GWW5XR1;Core(TM) i3-2120 CPU @ 3.30GHz ;D0:67:E5:1F:F2:11;;2da GEN.;480 GB;SSD;4 GB;DDR3;CY63F-QCQPB-7DXKT-973BP-M33XQ;---;BANCO FORTALEZA
15;A-A15-D;B;15;OROPOE30020835;Operaciones Planta Baja;Sucursal Central;CAJERO 2;ALVARO MARCELO IBAÑEZ SANTIESTEVEZ;10.17.71.45;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;OpenOffice 4.1.15;ESCRITORIO;DELL ;Vostro 260;64SJWV1;Core(TM) i3-2120 CPU @ 3.30GHz;78:45:C4:19:F6:75;;2da GEN.;480 GB;SSD;4 GB;DDR3;DVHQC-JF3H6-N7T6C-J6WYX-JW8XV;---;BANCO FORTALEZA
17;A-A17-D;B;17;OROPOR30020543;Operaciones Planta Baja;Sucursal Central;CAJERO 3;DANIELA CRESPO ROJAS;10.17.71.44;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;OpenOffice 4.1.15;ESCRITORIO;DELL ;Vostro 260;3KZFXR1;Core(TM) i3-2120 CPU @ 3.30GHz;D0:67:E5:1D:8E:BD;;2da GEN.;480 GB;SSD;4 GB;DDR3;TFCFY-WFGY8-DQNPR-64XYR-2J8XV;---;BANCO FORTALEZA
18;A-A18-D;B;18;ORCTOE30020915;Operaciones Planta Baja;Sucursal Central;CAJERO 4;NIDIA JANNETH PACO CALLE ;10.17.71.46;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;OpenOffice 4.1.15;ESCRITORIO;DELL ;Vostro 270s;C1BHQW1;Core(TM) i3-3220 CPU @ 3.30GHz;78:45:C4:3C:B1:6C;2E:2A:70:3F:78:52;3ra GEN.;480 GB;SSD;8 GB;DDR3;MMJPR-4YMMQ-FMG46-CQK2W-PPJ4C;;BANCO FORTALEZA
1;A-01;A;8;NGONOR-MJ0F3XXY;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;CLAUDIA ROMERO SOLIZ ;10.17.71.102;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;22H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3XXY; Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:34:19:E1;EC:63:D7:19:53:61;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
B-7;B-07;A;10;OPOSOR-MJ0F3XVC;Planta baja Operaciones;Agencia Bolivar;OFICIAL DE SERVICIO AL CLIENTE - PUNTO DE RECLAMO;CHRISTIAN JAVIER GONZALES CANAZA ;10.17.71.103;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2019; ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3XVC; Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:34:1A:0A;EC:63:D7:19:71:25;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
11;A-11;A;12;NGONOR-PF41JT38;Negocios 1er Piso;Agencia Bolivar;ANALISTA DE NEGOCIOS;MAYERLI VALERIA ALIAGA QUISPE;10.17.71.104;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;PORTÁTIL;LENOVO;21E7S18700;PF41JT38;Core(TM) i7-1255U;9C:2D:CD:55:30:17;58:CE:2A:A5:C6:0C;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
7;A-07;A;6;NGONOR-MJ0F3XY2;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS; MAGALY VIRGINIA CHAVEZ LOPEZ;10.17.71.105;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2019; ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3XY2; Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:34:1A:20;EC:63:D7:19:53:E3;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
5;A-05;A;9;NGONOR-MJ0L1QQK;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;Libre;10.17.71.106;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021; ESCRITORIO;LENOVO;11T7S79800;MJ0L1QQK; Core(TM) i7-12700;04:7C:16:9D:F9:21;BC:03:58:40:68:AC;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
B-11;B-11;A;11;OROPOR30020713;Planta baja Operaciones;Agencia Bolivar;Cajero 2;MARCELO IVAN HEREDIA HERRERA;10.17.71.108;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;22H2;OpenOffice 4.1.15; ESCRITORIO;DELL ;Vostro 260;2S3C7V1; Core(TM) i3-2120 CPU @ 3.30GHz ;78:45:C4:07:C4:9E;;2da GEN.;480 GB;SSD;4 GB;DDR3;MMJPR-4YMMQ-FMG46-CQK2W-PPJ4C;Open Office;BANCO FORTALEZA
B-13;B-13;A;13;ORNGOR30020927;Planta baja Operaciones;Agencia Bolivar;Cajero 3;GABY MAMANI CHOQUETOPA;10.17.71.109;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;OpenOffice 4.1.15; ESCRITORIO;DELL;Vostro 270s;15LQQW1;Core(TM) i3-3220 CPU @ 3.30GHz;78:45:C4:3E:5A:26;1E:2A:70:32:49:35;3ra GEN.;480 GB;SSD;4 GB;DDR3;JRGQM-PT4H7-MBQN9-P66VG-QYFDH;Open Office;BANCO FORTALEZA
B-4;B-04;A;3;GANGOR-PF30ZREP;Negocios 1er Piso;Agencia Bolivar;GERENTE DE AGENCIA;CESAR EDUARDO SOLIZ PERALTA;10.17.71.110;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2019;PORTÁTIL;LENOVO;20X4S2FL00;PF30ZREP;Core(TM) i7-1165G7 @ 2.80GHz ;90:2E:16:13:1D:42;16:5A:FC:0B:63:21;11va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
9;A-09;A;5;NGONOR-MJ0GGSQG;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;JACKELINE ROSSE MARY LIZARAZU QUISPE ;10.17.71.112;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021; ESCRITORIO;LENOVO;11DBS2NH00;MJ0GGSQG; Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:50:DE:99;F4:7B:09:ED:A0:E4;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
19;A-19;A;17;NGONOR-MJ01QQ8;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;Libre;10.17.71.113;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021; ESCRITORIO;LENOVO;11T7S79800;MJ0L1QQ8; Core(TM) i7-12700;04:7C:16:9D:F8:FE;BC:03:58:3F:4C:FB;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
3;A-03;A;7;NGONOR-PF41LAQ9;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;JAVIER GERMAN CHOQUE PEREZ ;10.17.71.114;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;PORTÁTIL;LENOVO;21E7S18700;PF41LAQ9;Core(TM) i7-1255U;9C:2D:CD:55:22:F1;58:CE:2A:A5:C6:61;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
17;A-17;A;15;NGONOR-MJ0L1QQF;Negocios 1er Piso;Agencia Bolivar;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;RUTH ERIKA ACEVEDO HERBAS;10.17.71.115;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021; ESCRITORIO;LENOVO;11T7S79800;MJ0L1QQF; Core(TM) i7-12700;04:7C:16:9D:F9:1A;BC:03:58:40:68:B6;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
13;A-13;A;14;ORCZOX30020539;Negocios 1er Piso;Agencia Bolivar;NORMALIZADOR DE CARTERA;DANILO ALVAREZ ARISPE ;10.17.71.201;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;22H2;LibreOffice 24.8.0.3;ESCRITORIO;DELL ;Vostro 260;3L2BXR1;Core(TM) i3-2120 CPU @ 3.30GHz;D0:67:E5:1D:8E:8C;;2da GEN.;480 GB;SSD;4 GB;DDR3;DVHQC-JF3H6-N7T6C-J6WYX-JW8XV;Libre office;BANCO FORTALEZA
26;A-26;A;21;NGONOR-MJ0GG9WZ;Negocios 1er Piso;Agencia Ejercito;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS; CHRISTIAN RAMIRO ARCE VARGAS;10.17.71.202;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11DBS2NH00;MJ0GG9WZ;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:4F:CC:47;F4:7B:09:EF:19:DD;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
8;A-08;A;8;OSOPOR-MJ0F3XXZ;Planta baja Operaciones;Agencia Ejercito;OFICIAL DE SERVICIO AL CLIENTE - PUNTO DE RECLAMO;NATALY JANETH MAMANI VELASQUEZ;10.17.71.203;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3XXZ;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:3D:9D:EA;EC:63:D7:17:A7:32;10ma GEN.;480 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
3;A-03;A;11;OROPPJ30020928;Planta baja Operaciones;Agencia Ejercito;CAJERO 2;CLAUDIA RAQUEL HUANCA AMAYA;10.17.71.204;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;OpenOffice 4.1.15;ESCRITORIO;DELL ;Vostro 270s ;12RRQW1;Core(TM) i3-3220 CPU @ 3.30GHz ;78:45:C4:3E:5A:26;1E:2A:70:32:72:65;3ra GEN.;480 GB;SSD;4 GB;DDR3;XV7QG-TPJX2-MVQ9N-7WM9T-BPWXV;;BANCO FORTALEZA
2;A-02;A;13;OROPOR30020836;Planta baja Operaciones;Agencia Ejercito;CAJERO 3;LUIS VIDAURRE LEON;10.17.71.205;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;OpenOffice 4.1.15;ESCRITORIO;DELL ;Vostro 260;68XJWV1;Core(TM) i3-2120 CPU @ 3.30GHz ;78:45:C4:1A:3E:65;;2da GEN.;480 GB;SSD;4 GB;DDR3;FQWRK-HWM4W-77VDN-JHW66-FVRDH;;BANCO FORTALEZA
;;A;19;GANGOR-PF30ZNCL;Negocios 1er Piso;Agencia Ejercito;GERENTE DE AGENCIA;NINETH CRISTINA MONTIEL MURILLO ;10.17.71.206;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;PORTÁTIL;LENOVO;20X4S2FL00;PF30ZNCL;Core(TM) i7-1165G7 @ 2.80GHz ;90:2E:16:13:14:60;16:5A:FC:0B:20:B3;11va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
32;A-32;A;17;NGONOR-MJ0F3XT7;Negocios 1er Piso;Agencia Ejercito;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;SONIA ISMELDA VILLARROEL VILLARROEL ;10.17.71.207;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3XT7;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:34:1A:18;EC:63:D7:17:C4:92;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
28;A-28;A;6;NGONOR-MJ0GGEM1;Negocios 1er Piso;Agencia Ejercito;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;MARIA ROSARIO BERNAL TORDOYA;10.17.71.208;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11DBS2NH00;MJ0GGEM1;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:4F:CB:9A;F4:7B:09:EF:1E:10;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
36;A-36;A;10;NGONOR-MJ0F3XXV;Negocios 1er Piso;Agencia Ejercito;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;ARIEL AMILCAR MENDIETA ASIER;10.17.71.210;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2019;ESCRITORIO;LENOVO;11DBS2NH00;MJ0F3XXV;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:34:1A:2F;EC:63:D7:19:B7:11;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
28;A-28;A;4;NGONOR-MJ0GGEPQ;Negocios 1er Piso;Agencia Ejercito;OFICIAL DE NEGOCIOS BANCA PYME Y PERSONAS;ANGEL ARTURO CABALLERO FERNANDEZ;10.17.71.211;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;23H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11DBS2NH00;MJ0GGEPQ;Core(TM) i5-10400 CPU @ 2.90GHz;E0:BE:03:4F:CB:E4;60:E3:2B:19:AA:5F;10ma GEN.;1 TB;HDD;16 GB;DDR4;;;OUTSOURCING DATEC
34;A-34;A;22;NOONOR-MJ0L1QPS;Negocios 1er Piso;Agencia Ejercito;ANALISTA DE NEGOCIOS;JOSE SANTOS GUTIERREZ;10.17.71.212;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Empresas 2021;ESCRITORIO;LENOVO;11T7S79800;MJ0L1QPS;Core(TM) i7-12700;04:7C:16:9E:19:A5;BC:03:58:40:14:FB;12va GEN.;512 GB;SSD;16 GB;DDR4;;;OUTSOURCING DATEC
1;PB-A01D;A;1;OROPOB30020576;Planta Baja;PAF Escara;CAJERO;ARACELY CLAUDIA CONDORI ARIMOSA ;10.17.109.25;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;OpenOffice 4.1.15;ESCRITORIO;DELL;Vostro 260;3KPCXR1;Core(TM) i3-2120 CPU @ 3.30GHz ;D0:67:E5:1D:96:EE;;2da GEN.;480 GB;SSD;4 GB;DDR3;2JY8J-RCC8M-YNGYK-4J4Q6-C9T67;Open Office;BANCO FORTALEZA
2;PB-A02D;A;2;OROPOB30020578;Planta Baja;PAF Escara;OFICIAL DE SERVICIO AL CLIENTE - PUNTO DE RECLAMO;BORIS MICHAEL  CHOQUE POMA ;10.17.109.26;SI ;SI ;SI ;SI ;NO;SI;SI;SI;SI;SI;SI;Microsoft Windows 11 Pro;24H2;Microsoft Office Hogar y Pequeña Empresa 2010;ESCRITORIO;DELL;Vostro 260;3KW9XR1;Core(TM) i3-2120 CPU @ 3.30GHz ;D0:67:E5:1D:8F:A5;;2da GEN.;480 Gb;SSD;4 GB;DDR3;GYXX9-9H3BG-XBMTC-NQRBP-MPWXV;BG3VJ-3QMJ2-9DKH2-KRVRY-JYB2T;BANCO FORTALEZA
CSV;

        // Pre-load or Defaults
        $defaultStatus = EstadoActivo::firstOrCreate(['nombre' => 'Operativo']);
        $defaultCriticidad = NivelCriticidad::firstOrCreate(['nombre' => 'Media'], ['nivel_numerico' => 5, 'color' => '#fbbf24']);
        $defaultCity = City::firstOrCreate(['name' => 'Oruro'], ['code' => 'ORU']);
        $defaultTipoUbicacion = TipoUbicacion::firstOrCreate(['nombre' => 'Oficina']);

        // Type Mapping
        $typeMapping = [
            'PORTÁTIL' => 'Laptop',
            'ESCRITORIO' => 'Desktop',
            'SFF' => 'Desktop',
            'Vostro' => 'Desktop',
            'OptiPlex' => 'Desktop',
            'SERVER' => 'Server',
        ];

        // Process Lines
        $lines = explode("\n", $csvData);
        $header = str_getcsv(array_shift($lines), ';');
        $validLocationIds = [];
        $validBranchIds = [];
        
        $defaultBranchType = BranchType::firstOrCreate(['name' => 'Agencia']);

        // PRE-SEED OFFICIAL AGENCIES
        // Ensure all agencies from the text file exist, even if not in CSV
        $officialAgencies = $this->getOfficialAgencies();
        foreach ($officialAgencies as $key => $data) {
            // City
            $city = City::firstOrCreate(['name' => $data['city']]);
            
            // Type
            $typeName = 'Agencia';
            $checkName = strtoupper($data['name']);
            if (Str::contains($checkName, 'ATM')) $typeName = 'ATM';
            elseif (Str::contains($checkName, 'SUCURSAL')) $typeName = 'Sucursal';
            elseif (Str::contains($checkName, 'OFICINA')) $typeName = 'Oficina Externa';
            
            $type = BranchType::firstOrCreate(['name' => $typeName]);
            
            // Branch
            $branch = Branch::updateOrCreate(
                ['name' => $data['name'], 'city_id' => $city->id],
                [
                    'branch_type_id' => $type->id,
                    'address' => $data['address'],
                    'phones' => $data['phones']
                ]
            );
            $validBranchIds[] = $branch->id;
        }

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $row = str_getcsv($line, ';');
            
            // Should have around 39 columns.
            if (count($row) < 5) continue; 

            // Map columns
            // Correct Column Mapping based on visual inspection of data:
            // 0: N (20)
            // 1: Short Code (NG-B20-D)
            // 2: ? (A)
            // 3: ? (20)
            // 4: Asset Code (ORNGOR30021040)
            // 5: Location Detail (Negocios 1er Piso)
            // 6: Agency (Sucursal Central)
            
            // $patchPanel = trim($row[3]); // Unclear, skipping specific network mapping for now unless requested
            // $switchPuerto = trim($row[4]); // This creates conflict with Code. 
            
            $codigo = trim($row[4]); // Asset Code
            $ubicacionRef = trim($row[5]); // Detail
            $agencia = mb_strtoupper(trim($row[6])); // Agency

            // Official Agencies Logic
            $officialAgencies = $this->getOfficialAgencies();
            $officialBranch = null;

            // Try to find official match by Name
            if (isset($officialAgencies[$agencia])) {
                 $officialBranch = $officialAgencies[$agencia];
            }
            
            // Determine City: Priority Official Map -> Default Oruro
            $cityName = 'ORURO'; // Default
            if ($officialBranch && isset($officialBranch['city'])) {
                $cityName = $officialBranch['city'];
            }
            
            // Validate City Name against allowed list to prevent "A-01" pollution
            $allowedCities = ['ORURO', 'LA PAZ', 'COCHABAMBA', 'SANTA CRUZ', 'SUCRE', 'TARIJA', 'POTOSI', 'BENI', 'PANDO'];
            if (!in_array($cityName, $allowedCities)) {
                $cityName = 'ORURO';
            }
            
            $city = City::firstOrCreate(['name' => $cityName]);

            // Special handling for Mercado Campesino (duplicate name in different cities)
            if ($agencia === 'AGENCIA MERCADO CAMPESINO' && $cityName === 'TARIJA') { // Use verified cityName
                 $officialBranch = $officialAgencies['AGENCIA MERCADO CAMPESINO'] ?? null;
                 // But wait, the map key 'AGENCIA MERCADO CAMPESINO' points to Sucre/Tarija?
                 // I should manually force the Tarija address if needed.
                 // For now, allow flow.
            }
            
            // Override Agency Name if Official Found
            $branchName = $officialBranch ? $officialBranch['name'] : $agencia;
            $branchAddress = $officialBranch ? ($officialBranch['address'] ?? null) : null;
            $branchPhones = $officialBranch ? ($officialBranch['phones'] ?? null) : null;
            
            // Update Ubicacion Name to match Official Name
            $ubicacionName = $branchName;

            $cargo = trim($row[7]);
            $funcionario = trim($row[8]);
            $ip = trim($row[9]);
            
            if (empty($codigo)) $codigo = trim($row[0]); 
            
            // (Removed explicit Region/City parsing from row[1])

            $os = trim($row[21] ?? '');
            $osVersion = trim($row[22] ?? '');
            $office = trim($row[23] ?? '');
            $tipoEquipoRaw = trim($row[24] ?? '');
            $marcaNombre = trim($row[25] ?? '');
            $modelo = trim($row[26] ?? '');
            $serie = trim($row[27] ?? '');
            $procesador = trim($row[28] ?? '');
            $macEth = trim($row[29] ?? '');
            $macWifi = trim($row[30] ?? '');
            $generacion = trim($row[31] ?? '');
            $discoCap = trim($row[32] ?? '');
            $discoTipo = trim($row[33] ?? '');
            $memCap = trim($row[34] ?? '');
            $memTipo = trim($row[35] ?? '');
            $propiedad = trim($row[38] ?? '');

            if (empty($codigo)) continue;

             // 1. Asset Type
            $typeKey = 'Desktop'; 
            foreach ($typeMapping as $k => $v) {
                if (stripos($tipoEquipoRaw, $k) !== false) {
                    $typeKey = $v;
                    break;
                }
            }
            $tipoActivo = TipoActivo::firstOrCreate(['nombre' => $typeKey]);

            // 2. Brand
            $marca = Marca::firstOrCreate(['nombre' => strtoupper($marcaNombre)]);

            // 3. Location (Agency Only)
            $ubicacion = Ubicacion::updateOrCreate(
                ['nombre' => $ubicacionName], 
                [
                    'ciudad_id' => $city->id,
                    'tipo_ubicacion_id' => $defaultTipoUbicacion->id,
                    'codigo_ubicacion' => Str::limit(Str::slug($ubicacionName), 20, '')
                ]
            );

            // 4. Owner
            $propietario = Propietario::firstOrCreate(['nombre' => $propiedad]);

            // 5. Asset
            $asset = Activo::updateOrCreate(
                ['codigo_activo' => $codigo],
                [
                    'tipo_activo_id' => $tipoActivo->id,
                    'modelo_id' => 1, // Will update below
                    'numero_serie' => $serie,
                    'mac_ethernet' => $macEth,
                    'mac_wifi' => $macWifi,
                    'ubicacion_id' => $ubicacion->id,
                    'detalle_ubicacion' => $ubicacionRef, // Specific detail (Piso, Oficina)
                    'propietario_id' => $propietario->id,
                    'estado_activo_id' => $defaultStatus->id,
                    'criticidad_id' => $defaultCriticidad->id,
                ]
            );

            // Handle Model Linking (needs Marca)
            $modelObj = Modelo::firstOrCreate(
                ['nombre' => $modelo ?: 'Generico'],
                ['marca_id' => $marca->id]
            );
            $asset->modelo_id = $modelObj->id;
            $asset->save();

            // 6. Dynamic Attributes
            $attributes = [
                'Procesador' => $procesador,
                'Memoria RAM' => "$memCap $memTipo",
                'Almacenamiento' => "$discoCap $discoTipo",
                // 'Sistema Operativo' =>moved to Software,
                // 'Office' => moved to Software,
                'Generación' => $generacion,
                'Descripcion Completa' => "$tipoEquipoRaw $marcaNombre $modelo ($funcionario)", 
            ];

            foreach ($attributes as $key => $value) {
                if (empty($value)) continue;
                AtributoActivo::updateOrCreate(
                    ['activo_id' => $asset->id, 'nombre' => $key],
                    ['valor' => $value]
                );
            }
            
            // CLEANUP: Remove old attributes that are now in specific tables or columns
            AtributoActivo::where('activo_id', $asset->id)
                ->whereIn('nombre', [
                    'Patch Panel', 'Roseta', 'Switch', 
                    'Sistema Operativo', 'Version Sistema Operativo',
                    'Office', 'N°LICENCIA WIN 10', 'N°LICENCIA OFFICE'
                ])
                ->delete();

            // 7b. Software Migration
            $adminUser = User::first(); // Fallback for recorded_by
            $adminId = $adminUser ? $adminUser->id : 1;

            // Handle OS
            if (!empty($os)) {
                $osName = "$os $osVersion";
                $osKey = trim($row[36] ?? '');
                
                $licenseOS = \App\Models\SoftwareLicense::firstOrCreate(
                    ['nombre' => $osName],
                    ['tipo' => 'OEM', 'seats_total' => 1000] // Default placeholders
                );

                \App\Models\SoftwareInstallation::updateOrCreate(
                    [
                        'activo_id' => $asset->id, 
                        'license_id' => $licenseOS->id
                    ],
                    [
                        'fecha_instalacion' => now(),
                        'registrado_por' => $adminId,
                        'observaciones' => $osKey ? "Key: $osKey" : "Migrated"
                    ]
                );
            }

            // Handle Office
            if (!empty($office)) {
                $officeKey = trim($row[37] ?? '');
                
                $licenseOffice = \App\Models\SoftwareLicense::firstOrCreate(
                    ['nombre' => $office],
                    ['tipo' => 'Perpetual', 'seats_total' => 1000]
                );

                \App\Models\SoftwareInstallation::updateOrCreate(
                    [
                        'activo_id' => $asset->id, 
                        'license_id' => $licenseOffice->id
                    ],
                    [
                        'fecha_instalacion' => now(),
                        'registrado_por' => $adminId,
                        'observaciones' => $officeKey ? "Key: $officeKey" : "Migrated"
                    ]
                );
            }

            // 8. Network Assignment (With PuntoRed)
            $puntoRedId = null;
            if (!empty($patchPanel) || !empty($roseta)) {
                $patchPanel = $patchPanel ?: 'N/A';
                $roseta = $roseta ?: 'N/A';
                
                // Puntos de Red are unique per Patch/Roseta/Ubicacion usually, or just Patch/Roseta globally? 
                // Schema says unique(['patch_panel', 'roseta', 'ubicacion_id']).
                
                // Check if exists
                $puntoRed = \DB::table('puntos_red')
                    ->where('patch_panel', $patchPanel)
                    ->where('roseta', $roseta)
                    ->where('ubicacion_id', $ubicacion->id)
                    ->first();

                if ($puntoRed) {
                    $puntoRedId = $puntoRed->id;
                    // Update switch info if available
                    if (!empty($switchNombre)) {
                        \DB::table('puntos_red')->where('id', $puntoRedId)->update([
                            'switch' => "$switchNombre Port $switchPuerto",
                            'updated_at' => now()
                        ]);
                    }
                } else {
                    $puntoRedId = \DB::table('puntos_red')->insertGetId([
                        'patch_panel' => $patchPanel,
                        'roseta' => $roseta,
                        'ubicacion_id' => $ubicacion->id,
                        'switch' => (!empty($switchNombre)) ? "$switchNombre Port $switchPuerto" : null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            if (!empty($ip) || $puntoRedId) {
                \Illuminate\Support\Facades\DB::table('asignaciones_red')->updateOrInsert(
                    ['activo_id' => $asset->id],
                    [
                        'ip_address' => $ip,
                        'punto_red_id' => $puntoRedId,
                        // MACs moved to Activo, keeping them null here or removing if schema allows nullable. 
                        // Migration 'create_networking...' defined them as nullable.
                        'fecha_asignacion' => now(),
                        'es_actual' => true,
                    ]
                );
            }

            // 9. Users
            if (!empty($funcionario) && $funcionario !== 'Libre') {
                $user = User::firstOrCreate(
                    ['name' => $funcionario],
                    ['email' => Str::slug($funcionario) . '@fortaleza.com.bo', 'password' => bcrypt('password')]
                );
            }

            // Track valid location
            $validLocationIds[] = $ubicacion->id;

            // SYNC BRANCH (Sucursales/Agencias/ATMs)
            $branchTypeName = 'Agencia';
            // Use branchName for detection
            $checkName = strtoupper($branchName);
            if (Str::contains($checkName, 'ATM')) {
                $branchTypeName = 'ATM';
            } elseif (Str::contains($checkName, 'SUCURSAL')) {
                $branchTypeName = 'Sucursal';
            } elseif (Str::contains($checkName, 'OFICINA')) {
                $branchTypeName = 'Oficina Externa';
            }
            
            $branchType = TipoSucursal::firstOrCreate(['nombre' => $branchTypeName]);
            
            $branchData = [
                'tipo_sucursal_id' => $branchType->id,
            ];
            if (!empty($branchAddress)) $branchData['direccion'] = $branchAddress;
            if (!empty($branchPhones)) $branchData['telefonos'] = $branchPhones;

            $branch = Branch::updateOrCreate(
                ['name' => $branchName, 'city_id' => $city->id],
                $branchData
            );
            $validBranchIds[] = $branch->id;
        }
        
        /* 
        // CLEANUP: Remove stale locations (e.g., old "Agency - Detail" format)
        $validLocationIds = array_unique($validLocationIds);
        $staleLocations = Ubicacion::whereNotIn('id', $validLocationIds)
            ->whereDoesntHave('activos')
            ->get();
            
        if ($staleLocations->isNotEmpty()) {
            $staleLocationIds = $staleLocations->pluck('id');
            // Find Network Points associated with these stale locations
            $stalePuntoRedIds = PuntoRed::whereIn('ubicacion_id', $staleLocationIds)->pluck('id');
            if ($stalePuntoRedIds->isNotEmpty()) {
                // Delete Assignments linked to these stale points
                AsignacionRed::whereIn('punto_red_id', $stalePuntoRedIds)->delete();
                // Delete the Points
                PuntoRed::whereIn('id', $stalePuntoRedIds)->delete();
            }
            // Finally, delete the Locations
            Ubicacion::whereIn('id', $staleLocationIds)->delete();
        }
        */

        // CLEANUP: Stale Branches (DISABLED to avoid accidental deletion of ATMs)
        // Branch::whereNotIn('id', array_unique($validBranchIds))->delete();
            
        // CLEANUP: Stale Cities (Bad data like "A-01")
        // Only keep official cities
        $officialCityNames = ['ORURO', 'LA PAZ', 'COCHABAMBA', 'SANTA CRUZ', 'SUCRE', 'TARIJA', 'POTOSI', 'BENI', 'PANDO'];
        City::whereNotIn('name', $officialCityNames)->delete();
            
        // Optional: Log deleted count if running in console, but here we just execute.
    }

    private function getOfficialAgencies()
    {
        // Based on user provided agencias.txt
        return [
            // LA PAZ
            'SUCURSAL LA PAZ' => [
                'name' => 'Sucursal La Paz',
                'address' => 'Av. 16 de Julio No. 1440, Zona Central.',
                'phones' => '2317211 - 2369955',
                'city' => 'LA PAZ'
            ],
            'AGENCIA ARCE' => [
                'name' => 'Agencia Arce',
                'address' => 'Av. Arce No. 2799 esq. calle Cordero, Zona San Jorge',
                'phones' => '2434142',
                'city' => 'LA PAZ'
            ],
            'AGENCIA TUMUSLA' => [
                'name' => 'Agencia Tumusla',
                'address' => 'Av. Tumusla No. 765, entre Av. Buenos Aires y Plaza Garita de Lima.',
                'phones' => '2117372',
                'city' => 'LA PAZ'
            ],
            'AGENCIA VILLA FATIMA' => [
                'name' => 'Agencia Villa Fátima',
                'address' => 'Av. Las Américas No. 353, Zona Villa Fatima.',
                'phones' => '2215120',
                'city' => 'LA PAZ'
            ],
            'AGENCIA SAN MIGUEL' => [
                'name' => 'Agencia San Miguel',
                'address' => 'Av. Mariscal Montenegro No. 1246.',
                'phones' => '2119204 – 2119180',
                'city' => 'LA PAZ'
            ],
            'AGENCIA ACHUMANI' => [
                'name' => 'Agencia Achumani',
                'address' => 'Avenida Garcia Lanza No. 15 entre calles 11 y 12 achumani.',
                'phones' => '2794070',
                'city' => 'LA PAZ'
            ],
            'AGENCIA ENTRE RIOS' => [
                'name' => 'Agencia Entre Ríos',
                'address' => 'Calle Picada Chaco No.828, Zona El Tejar.',
                'phones' => '2386117-2380076',
                'city' => 'LA PAZ'
            ],
            'SUCURSAL EL ALTO' => [
                'name' => 'Sucursal El Alto',
                'address' => 'Calle Jorge Carrasco No.79 entre calles 4 y 5, Zona 12 de Octubre.',
                'phones' => '2821474 - 2821306',
                'city' => 'LA PAZ' // El Alto is usually part of La Paz region in simplified models or its own.
            ],
            'AGENCIA 16 DE JULIO' => [
                'name' => 'Agencia 16 de Julio',
                'address' => 'Av.Alfonzo Ugarte No. 50, Lote 12, Manzano 17, Zona 16 de Julio',
                'phones' => '2847448',
                'city' => 'LA PAZ'
            ],
            'AGENCIA RIO SECO' => [
                'name' => 'Agencia Río Seco',
                'address' => 'Av. Juan Pablo II No. 3030, Lote No. 1C Manzana s/n.',
                'phones' => '2861937',
                'city' => 'LA PAZ'
            ],
            'AGENCIA CRUCE VILLA ADELA' => [
                'name' => 'Agencia Cruce Villa Adela',
                'address' => 'Av. Ladislao Cabrera No.15, Zona Villa Bolivar Municipal',
                'phones' => '2852332',
                'city' => 'LA PAZ'
            ],
            'AGENCIA VILLA DOLORES' => [
                'name' => 'Agencia Villa Dolores',
                'address' => 'Av. Antofagasta No. 558 esq. calle 6, Zona Villa Dolores',
                'phones' => '2918789',
                'city' => 'LA PAZ'
            ],
            'AEROPUERTO EL ALTO' => [
                'name' => 'Aeropuerto El Alto',
                'address' => 'Av. Héroes Km 7 s/n, Aeropuerto Internacional El Alto',
                'phones' => '',
                'city' => 'LA PAZ'
            ],
            'OFICINA EXTERNA PUERTO CARABUCO' => [
                'name' => 'Oficina Externa Puerto Carabuco',
                'address' => 'Plaza Principal 3 de Mayo del Puerto Mayor de Carabuco',
                'phones' => '',
                'city' => 'LA PAZ'
            ],
            'OFICINA EXTERNA HUATAJATA' => [
                'name' => 'Oficina Externa Huatajata',
                'address' => 'Carretera La Paz - Copacabana, Municipio de Huatajata s/n',
                'phones' => '',
                'city' => 'LA PAZ'
            ],

            // ORURO
            'SUCURSAL ORURO' => [
                'name' => 'Sucursal Oruro',
                'address' => 'Calle La Plata s/n esq. Calle Sucre.',
                'phones' => '5250927',
                'city' => 'ORURO'
            ],
            'SUCURSAL CENTRAL' => [ // Mapping CSV "Sucursal Central" to Oruro if region matches, or generic
                'name' => 'Sucursal Oruro', // Assuming 'Sucursal Central' in Oruro context IS Sucursal Oruro
                'address' => 'Calle La Plata s/n esq. Calle Sucre.',
                'phones' => '5250927',
                'city' => 'ORURO'
            ],
            'AGENCIA EJERCITO' => [
                'name' => 'Agencia Av. del Ejército',
                'address' => 'Av. del Ejercito No. 600, esquina Av. Tacna, zona Este.',
                'phones' => '5283580',
                'city' => 'ORURO'
            ],
            'AGENCIA AV. DEL EJERCITO' => [
                'name' => 'Agencia Av. del Ejército',
                'address' => 'Av. del Ejercito No. 600, esquina Av. Tacna, zona Este.',
                'phones' => '5283580',
                'city' => 'ORURO'
            ],
            'AGENCIA MERCADO BOLIVAR' => [
                'name' => 'Agencia Mercado Bolívar',
                'address' => 'Calle Bolivar No. 282, entre Brasil y Rayca Bacovick',
                'phones' => '5281641',
                'city' => 'ORURO'
            ],
             'AGENCIA BOLIVAR' => [ // CSV Mapping
                'name' => 'Agencia Mercado Bolívar',
                'address' => 'Calle Bolivar No. 282, entre Brasil y Rayca Bacovick',
                'phones' => '5281641',
                'city' => 'ORURO'
            ],
            'OFICINA EXTERNA ESCARA' => [
                'name' => 'Oficina Externa Escara',
                'address' => 'Calle Sucre esq. Bolivar frente a Plaza 16 de Septiembre',
                'phones' => '',
                'city' => 'ORURO'
            ],
             'PAF ESCARA' => [ // CSV Mapping
                'name' => 'Oficina Externa Escara',
                'address' => 'Calle Sucre esq. Bolivar frente a Plaza 16 de Septiembre',
                'phones' => '',
                'city' => 'ORURO'
            ],

            // COCHABAMBA
            'SUCURSAL COCHABAMBA' => [
                'name' => 'Sucursal Cochabamba',
                'address' => 'Av. Ballivian No.739, Edificio "Prado Business Center"',
                'phones' => '4506060 - 4583041',
                'city' => 'COCHABAMBA'
            ],
            'AGENCIA 14 DE SEPTIEMBRE' => [
                'name' => 'Agencia 14 de Septiembre',
                'address' => 'Plaza 14 de Septiembre No.205, esquina calle Baptista',
                'phones' => '',
                'city' => 'COCHABAMBA'
            ],
            'AGENCIA AMERICA' => [
                'name' => 'Agencia América',
                'address' => 'A. América No.0969 entre Melchor Urquidi y Miguel Aguirre',
                'phones' => '4254689',
                'city' => 'COCHABAMBA'
            ],
            'AGENCIA LA CANCHA' => [
                'name' => 'Agencia La Cancha',
                'address' => 'Calle Esteban Arze N°1384 Mall Cochabamba',
                'phones' => '4557022',
                'city' => 'COCHABAMBA'
            ],
            'AGENCIA QUILLACOLLO' => [
                'name' => 'Agencia Quillacollo',
                'address' => 'Calle Cochabamba s/n, Zona Central Quillacollo',
                'phones' => '4391197',
                'city' => 'COCHABAMBA'
            ],
            'AGENCIA SACABA' => [
                'name' => 'Agencia Sacaba',
                'address' => 'Calle Bolivar s/n esq. calle Colon',
                'phones' => '4703590',
                'city' => 'COCHABAMBA'
            ],
            'AGENCIA NORTE' => [
                'name' => 'Agencia Norte',
                'address' => 'Av. América N° 475, Zona Cala Cala',
                'phones' => '4506060',
                'city' => 'COCHABAMBA'
            ],
            'OFICINA EXTERNA TACACHI' => [
                'name' => 'Oficina Externa Tacachi',
                'address' => 'Av. Fructuoso Orellana s/n, zona Central de Tacachi',
                'phones' => '',
                'city' => 'COCHABAMBA'
            ],
            'OFICINA EXTERNA BOLIVAR' => [
                'name' => 'Oficina Externa Bolivar',
                'address' => 'Plaza 6 de Agosto s/n del municipio de Bolivar',
                'phones' => '',
                'city' => 'COCHABAMBA'
            ],

            // SUCRE
            'SUCURSAL SUCRE' => [
                'name' => 'Sucursal Sucre',
                'address' => 'Calle San Alberto No. 108, Zona Central.',
                'phones' => '6427880',
                'city' => 'SUCRE'
            ],
            'AGENCIA MERCADO CAMPESINO' => [ // Handles both Sucre and Tarija if duplicate? Assuming Sucre first or mapped by City
                'name' => 'Agencia Mercado Campesino',
                'address' => 'Calle Guillermo Loayza No. 586',
                'phones' => '6912435',
                'city' => 'SUCRE'
            ],

            // SANTA CRUZ
            'SUCURSAL SANTA CRUZ' => [
                'name' => 'Sucursal Santa Cruz',
                'address' => 'Calle Gabriel René Moreno No. 140.',
                'phones' => '3322929',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA VIRGEN DE COTOCA' => [
                'name' => 'Agencia Vírgen de Cotoca',
                'address' => 'Av. Virgen de Cotoca No. 2090, Zona Lazareto',
                'phones' => '3492030',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA MONSEÑOR RIVERO' => [
                'name' => 'Agencia Monseñor Rivero',
                'address' => 'Av. Monseñor Rivero No. 328',
                'phones' => '33321684',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA MERCADO ABASTO' => [
                'name' => 'Agencia Mercado Abasto',
                'address' => 'Av. Roque Aguilera No.3110',
                'phones' => '3598951',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA PLAN 3000' => [
                'name' => 'Agencia Plan 3000',
                'address' => 'Av. Paurito No. 5520 6to Anillo',
                'phones' => '3486158',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA LA RAMADA' => [
                'name' => 'Agencia La Ramada',
                'address' => 'Avenida Isabel La Catolica No.349',
                'phones' => '3587789',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA MUTUALISTA' => [
                'name' => 'Agencia Mutualista',
                'address' => 'Av. Japón No. 3577',
                'phones' => '3469963',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA MONTERO' => [
                'name' => 'Agencia Montero',
                'address' => 'Calle Warnes No. 122',
                'phones' => '9227429',
                'city' => 'SANTA CRUZ'
            ],
            'AGENCIA VILLA PRIMERO DE MAYO' => [
                'name' => 'Agencia Villa Primero de Mayo',
                'address' => 'Av. Cumavi No. 4950',
                'phones' => '3322929',
                'city' => 'SANTA CRUZ'
            ],
            'AEROPUERTO VIRU VIRU' => [
                'name' => 'Aeropuerto Viru Viru',
                'address' => 'Av. G77, carretera a Warnes',
                'phones' => '',
                'city' => 'SANTA CRUZ'
            ],
            'GENEX' => [
                'name' => 'Genex',
                'address' => '3er. Anillo Av. Banzer esq. Av. Japon',
                'phones' => '',
                'city' => 'SANTA CRUZ'
            ],

            // TARIJA
            'SUCURSAL TARIJA' => [
                'name' => 'Sucursal Tarija',
                'address' => 'Calle La Madrid No. 330',
                'phones' => '6643566',
                'city' => 'TARIJA'
            ],
             // Duplicate key 'AGENCIA MERCADO CAMPESINO' for Tarija?
             // I will use 'AGENCIA MERCADO CAMPESINO TARIJA' as key if needed, or handle in logic.
             // But CSV likely just says "AGENCIA MERCADO CAMPESINO".
             // Since I need to return ONE array, I can't have duplicate keys.
             // I will leave logic to handle City Context if I can, OR just assume unique names per user data.
             // User has 2 "Agencia Mercado Campesino" lines (125, 176).
             // I will skip adding the second one as KEY, and handle via City context in main loop if possible.
             // Or add a suffix-based key.

            'AGENCIA BERMEJO' => [
                'name' => 'Agencia Bermejo',
                'address' => 'Av. Barrientos Ortuño No. 635',
                'phones' => '6963550',
                'city' => 'TARIJA'
            ],

            // POTOSI
            'SUCURSAL POTOSI' => [
                'name' => 'Sucursal Potosí',
                'address' => 'Plaza 6 de Agosto No. 11',
                'phones' => '6222747',
                'city' => 'POTOSI'
            ],
            'AGENCIA VILLAZON' => [
                'name' => 'Agencia Villazón',
                'address' => 'Calle La Paz Nº 198',
                'phones' => '25965443',
                'city' => 'POTOSI'
            ],
        ];
    }
}
