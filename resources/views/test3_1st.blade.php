<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List - NAME / DATE</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f0f0;
            padding: 15px;
            font-size: 12px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            border: 2px solid #333;
            padding: 12px 14px 18px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid #333;
            font-weight: bold;
            font-size: 14px;
        }
        .header .date-side {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header .date-line {
            border-bottom: 1px solid #333;
            width: 140px;
            height: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        td {
            border: 1px solid #555;
            padding: 3px 4px;
            vertical-align: middle;
            height: 23px;
        }
        .name {
            text-align: left;
            font-size: 11.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background: #fafafa;
            width: 18%;
        }
        .empty {
            width: 32px;
            background: #fff;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .highlight {
            font-weight: bold;
            color: #111;
        }
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; border: 1px solid #000; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <span>NAME:</span>
        <div class="date-side">
            <span>DATE:</span>
            <div class="date-line"></div>
        </div>
    </div>

    <table>
        <!-- Row pattern: Weight | NAME | Qty | Weight | NAME | Qty | Weight | NAME | Qty | Weight | NAME | Qty -->
        <tr>
            <td class="empty"></td><td class="name highlight">ACELGAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">COLIFLOR</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANDAR RAMA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PERA LIMO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">AGUACATE C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">DATIL 1€ 1KG</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANGO C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PEREJIL</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">AGUACATE D</td><td class="empty"></td>
            <td class="empty"></td><td class="name">EDO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANGO OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PICANTE AFRI</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">AGUACATE OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ENDIVIAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MA DONCELLA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PICANTE BAN</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">AJETE</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ENELDO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANZANA FUJI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PICOTAS</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">AJO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ENSALADA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MA GOLDEN C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIMIENT ITA C</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">AJO BUTI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ESCAROLA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">M GOLDEN OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIMIENT ITA B</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">AJO PELADO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ESPARRAGOS C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN GRAN C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIMI ITA ROJO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">ALBAHACA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ESPARRAGOS B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN GRAN OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIMI PADRON</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">ALBARICOQUE</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">ESPINACA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN KANZI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIMI ROJO C</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">ALCACHOFAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ESPINACA MAN</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN PERLIM</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PIMI ROJO OFF</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">ALOVERA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">FRAMBUESA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN PINK</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIMI VERDE C</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">APIO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">FRESAS BAN</td><td class="empty"></td>
            <td class="empty"></td><td class="name">M REINETA C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIM VERDE OFF</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">ARANDANOS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">FRESAS C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">M REINETA OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIÑA C</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BANANAS C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">FRESAS B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN ROYEL C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PIÑA DEL MON</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">BANANAS B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">GOYAVA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">MA ROYEL OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PIÑA OFF</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BATATA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">GRANADA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN STARKING</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PITHAYA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BATATA ROJA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">GRANADILLA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAN VALVEN</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PLATANO M B-</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BERENJENA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">GRELOS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELOCOTON C</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PLATANO V B-</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BERENJENA LA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">GUANTES</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELOCOTON B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">POMELO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BERENJEN PEQ</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">GUINDIAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELOCOTON R</td><td class="empty"></td>
            <td class="empty"></td><td class="name">POMELO CHIN</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BOLSA B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">GUINEO VERDE</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELON C</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PUERRO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BOLSA RO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">HABAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELON OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">RABANO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BOLSA V</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">HIERBABUENA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELON CANTA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">RAIZ DE APIO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">BROCOLI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">HIGOS B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MELON GALIA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">REMO COCIDO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CALABACIN</td><td class="empty"></td>
            <td class="empty"></td><td class="name">HIGOS CHUM</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MENBRILLO</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">REMO CRUDO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CALABAZA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">HIGOS N</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MORA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">REPOLLO</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CALABAZA AUY</td><td class="empty"></td>
            <td class="empty"></td><td class="name">HIGOS SECO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NABO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ROLLO DE MAC</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CALABAZA PEQ</td><td class="empty"></td>
            <td class="empty"></td><td class="name">HINOJO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NABO RAMA/CHINA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">RUKULA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">CANARIAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">JENGIBRE</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">NARANJA C RA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">SANDIA B</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CANONIGOS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">JUDIA BOBBY</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NARANJA OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">SANDIA F</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CASTAÑA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">JUDIA LARGA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NARANJ ZUMO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">SANDIA MAR</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">CEBOLLAS B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">JUDIA VERD C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NARANJA B</td><td class="empty"></td>
            <td class="empty"></td><td class="name">SANDIA NEGRA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEBOLLA BUTI</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">JUDIA VER OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NARANJ BOL C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">SANDIA PLASE</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEBOLLAS DUL</td><td class="empty"></td>
            <td class="empty"></td><td class="name">KAKI B-</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">NARANJ BOL B</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">SANDIA ROLLO G P</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEBOLLAS G</td><td class="empty"></td>
            <td class="empty"></td><td class="name">KIWI B-</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NECTARINA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">SETAS BAND</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">CEBOLLA ROJA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">KIWI GOLDEN</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NISCALOS</td><td class="empty"></td>
            <td class="empty"></td><td class="name">SETAS S</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEBOLLATA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">KIWI ZESPRI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NISPERO</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">SOPA JULIANA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEBOLLA FINA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">KOROLA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">NUECES</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE CHER</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEBOLLATA LA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">LECHUGA BAT</td><td class="empty"></td>
            <td class="empty"></td><td class="name">OKRA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE ENSA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CEREZAS</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">LECHUGA COG</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PAK CHOI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE KUMA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CHAMPIÑON</td><td class="empty"></td>
            <td class="empty"></td><td class="name">LECHUGA ISA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PAPAYA C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE OFF</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">CHAMPI BAN</td><td class="empty"></td>
            <td class="empty"></td><td class="name">LECHUGA LAR</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PAPAYA OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE PERA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">CHAMPI COR</td><td class="empty"></td>
            <td class="empty"></td><td class="name">LEGUSTAN</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PARAGUAYA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE RAF</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CHIRIMOLLA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">LIMA C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PATATA AGRIA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE RAMA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CHIRIVIA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">LIMA OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PATATA BOLSA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">TOMATE ROSA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CIDRA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">LIMON</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">PATATA LAVA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">UVAS BANDEJA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name highlight">CILANTRO</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">LIMON BOL</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PATATA ROJA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">UVAS BLAN C</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CIRUELAS AMA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">LOMBARDA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PATATA SUCIA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">UVAS NEGR C</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CIRUELA CLAU</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MAIZ COCIDO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PEPINO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">UVAS PEQ</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CIRUELAS N</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">MAIZ VERDE S</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PERA AGUA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">YAME</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">CIRUELAS R</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANDARINA C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PERA CHINA</td><td class="empty"></td>
            <td class="empty"></td><td class="name highlight">YUCA C B-</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">COCO</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANDARI NOR</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PERA CON C</td><td class="empty"></td>
            <td class="empty"></td><td class="name">ZANAHORIA</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">COL CHINA</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANDARI OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PERA CON OFF</td><td class="empty"></td>
            <td class="empty"></td><td class="name">BANDEJA P M G</td><td class="empty"></td>
        </tr>
        <tr>
            <td class="empty"></td><td class="name">COL DE BRUSE</td><td class="empty"></td>
            <td class="empty"></td><td class="name">MANDAR ORRI</td><td class="empty"></td>
            <td class="empty"></td><td class="name">PERA ERCOLIN</td><td class="empty"></td>
            <td class="empty"></td><td class="name"></td><td class="empty"></td>
        </tr>
    </table>
</div>
</body>
</html>