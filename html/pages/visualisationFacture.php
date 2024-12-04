<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    table {
        table-layout: fixed;
        width: 100%;
    }

    #infoFactureAbonnement,
    #infoFactureOption {
        border-collapse: collapse;
        border: 3px solid black;
        text-align: center;
    }

    #infoFactureAbonnement>tbody>tr>td,
    #infoFactureAbonnement>tbody>tr>th,
    #infoFactureOption>tbody>tr>td,
    #infoFactureOption>tbody>tr>th {
        border-collapse: collapse;
        border: 1px solid black;
    }

    /* #infoFactureAbonnement > thead > tr > th, #infoFactureOption > thead > tr > th{
        border-collapse: collapse;
        border: 1px solid black;
        padding: 5px;
    } */

    #infoFactureAbonnement>thead>tr>td,
    #infoFactureAbonnement>tbody>tr>th,
    #infoFactureOption>thead>tr>td,
    #infoFactureOption>tbody>tr>th {
        border-collapse: collapse;
        border: 1px solid black;
    }

    #infoClient {
        background-color: #212121;
        color: white;
        padding: 20px;
    }

    tr {
        padding: 10px;
    }

    td {
        padding: 10px;
    }

    th {
        padding: 10px;
    }

    .divTab {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        margin: 30px;
    }

    .divTexte{
        position: relative;
        left: 20%;
    }

    h1 {
        text-align: center;
        margin: 30px;
    }
</style>

<body>
    <h1>Je suis une Facture test</h1>

    <div class="divTab">
        <table id="infoClient">
            <thead>
                <tr>
                    <th>Client</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nom :</td>
                </tr>
                <tr>
                    <td>Adress :</td>
                </tr>
                <tr>
                    <td>Numéro SIREN :</td>
                </tr>
                <tr>
                    <td>Numéro de téléphone :</td>
                </tr>
                <tr>
                    <td>Adresse mail :</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="divTexte">
        <p>facture N°0001</p>
        <p>date : XX/XX/XXXX</p>
    </div>
    <div class="divTab">
        <table id="infoFactureAbonnement">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Nombre</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Standard</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Premium</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    <td>1222220000</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="divTab">
        <table id="infoFactureOption">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Nombre</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Standard</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Premium</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Total</td>
                    <td>1222220000</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="divTexte">
        <p> date règlement : </p>
        
    </div>
</body>

</html>