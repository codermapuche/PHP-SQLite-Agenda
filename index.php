<!DOCTYPE html>
<html>
    <head>
        <title>Agenda SQLite</title>
        <script>
            function addRow(list_id)
            {
                var list = document.getElementById(list_id);
                var nuevo = list.getElementsByTagName("li")[0].cloneNode(true);
                var input = nuevo.getElementsByTagName("input")[0];
                input.value = "";
                input.required = false;
                list.appendChild(nuevo)
            }
        </script>
        <style>
            body 
            {
                font-family: "Helvetica Neue", Helvetica, Arial;
                font-size: 14px;
                line-height: 20px;
                font-weight: 400;
                -webkit-font-smoothing: antialiased;
                font-smoothing: antialiased;
            }

            /*/
             * Datatatable Style
            /*/
            table.datatatable 
            {
                color: #333;
                font-family: sans-serif;
                font-size: .9em;
                font-weight: 300;
                text-align: left;
                line-height: 35px;
                border-spacing: 0;
                border: 2px solid #428BCA;
                width: 98%;
                margin: 1%;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                display: table;
            }
            
            @media screen and (max-width: 580px) 
            {
                table.datatatable
                {
                    display: block;
                }
                
                table.datatatable > tbody > tr
                {
                    padding: 8px 0;
                    display: block;
                }
                
                table.datatatable > tbody > tr:not(:last-child) 
                {
                    border-bottom: 2px solid #DDDDDD !important; 
                }
                
                table.datatatable > tbody > tr > td, 
                table.datatatable > tfoot > tr > th
                {
                    padding: 2px 12px;
                    display: block;
                }
                
                table.datatatable > tfoot > tr:first-child > th:not(:first-child):not(:last-child)
                {
                    border-bottom: 1px dotted #DDDDDD;                 
                }
                table.datatatable > tfoot > tr:first-child > th:not(:first-child)
                {
                    border-top: none;              
                }
                
                table.datatatable > thead > tr:last-child
                {
                    display: none;
                }
                
                table.datatatable > thead > tr > th.actions,
                table.datatatable > tbody > tr > td.actions
                {
                    border-left: none !important; 
                    width: 100% !important;
                }
            }
            
            table.datatatable > thead tr:first-child 
            {
                background: #428BCA;
                color: #fff;
                border: none;
            }

            table.datatatable > thead > tr > th 
            {
                font-weight: bold;
                overflow: hidden;     
            }
            
            table.datatatable > tbody > tr > td 
            {                
                padding-right: 10px;
            }
            
            table.datatatable th:first-child, table.datatatable td:first-child 
            {
                padding: 0 10px 0 20px;
            }

            table.datatatable > thead > tr:last-child th 
            {
                border-bottom: 2px solid #DDDDDD; 
                text-align: center;
            }

            table.datatatable > tbody > tr:not(:last-child) > td
            {
                border-bottom: 1px dotted #DDDDDD; 
            }
            
            table.datatatable > tbody > tr 
            {
                display: table-row;
                background: #FFFFFF;
            }
            
            table.datatatable > tbody > tr:nth-of-type(odd) 
            {
                background: #FCFCFC;
            }
            
            table.datatatable > tbody > tr:hover 
            {
                background-color: #F6F6F6;
            }

            table.datatatable > tfoot > tr:first-child th 
            {
                border-top: 2px solid #DDDDDD; 
                text-align: center;
            }
            
            table.datatatable > thead tr:first-child > th .button
            {        
                padding: 0 15px;
                margin: 0 0 0 5px;
                color: #333;
                display: inline-block;
                font-size: .9em;
                line-height: 33px;
                text-decoration: none;
                float: right;
                background: #F6F6F6;
                text-transform: uppercase;
            }
            
            table.datatatable > thead tr:first-child > th .button:not(.active) 
            {               
                background: rgba(100, 100, 100, .20);
                color: #FFF;
                border: none;
            }
            
            table.datatatable > thead tr:first-child > th .button.active:hover
            {               
                color: #333;
            }
            
            table.datatatable > thead tr:first-child > th .button:not(.active):hover 
            {               
                background: rgba(50, 50, 50, .20);
            }
            table.datatatable > thead > tr > th.actions,
            table.datatatable > tbody > tr > td.actions,
            table.datatatable > tfoot > tr > th.actions
            {
                border-left: 1px dotted #DDDDDD; 
                width: 125px;
                text-align: center;
                text-transform: uppercase;
            }
            table.datatatable > tbody > tr > td .button,            
            table.datatatable > tfoot > tr > th:last-child .button 
            {
                color: #333;
                display: inline-block;
                font-size: .9em;
                padding: 0 5px;
                line-height: 30px;
                text-align: center;
                text-decoration: none;
                background: #F6F6F6;
                text-transform: uppercase;
                margin-right: -4px;
            }
            
            table.datatatable > tbody > tr > td .button:not(:last-child), 
            table.datatatable > tfoot > tr > th:last-child .button:not(:last-child)  
            {
                border-right: 1px solid #DDD;
            }
            
            table.datatatable > tbody > tr > td .button:hover, 
            table.datatatable > tfoot > tr > th:last-child .button:hover 
            {
                background: #EEE;
                color: #428BCA;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
<?php
    error_reporting(E_ALL);

    // Ubicacion de la base de datos:
    define("_FILE_DB_", "agenda.db");
    define("_NEW_DB_", !file_exists(_FILE_DB_));
    $db = new SQLite3(_FILE_DB_);

    // En caso de que sea una base nueva, esto la crea.
    if(_NEW_DB_)
    {
        $db->exec("CREATE TABLE persona 
                    (
                        id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, 
                        nombre STRING
                    );");
        $db->exec("CREATE TABLE telefono 
                    (
                        id_persona INTEGER NOT NULL REFERENCES persona(id) ON UPDATE CASCADE ON DELETE CASCADE, 
                        telefono STRING
                    );");
        $db->exec("CREATE TABLE email 
                    (
                        id_persona INTEGER NOT NULL REFERENCES persona(id) ON UPDATE CASCADE ON DELETE CASCADE, 
                        email STRING
                    );");
    }
    $db->exec("PRAGMA foreign_keys = ON;"); // Habilitar el uso de las claves foraneas que definimos.
    
    // Hay que borrar la persona.
    if(isset($_GET["borrar"]))
    {
        // Eliminar la persona.
        $stmt = $db->prepare("DELETE FROM persona WHERE id = :id;"); // Preparar la sentencia.
        if(!$stmt) exit($db->lastErrorMsg()); // Morir en el intento si no puede prepararla.
        $stmt->bindValue(":id", $_GET["borrar"], SQLITE3_INTEGER); // Evitar problemas de inyeccion sql y de sintaxis.
        $stmt->execute();
    }

    // Es un formulario, hay que guardar los datos.
    if(isset($_POST["save"]))
    {
        /*/
         * @param int $_POST["id"] -> Id del contacto, vacio si es uno nuevo.
         * @param string $_POST["nombre"] -> Nombre del contacto.
         * @param array $_POST["tel"] -> Array que contiene los telefonos.
         * @param array $_POST["email"] -> Array que contiene los emails.
        /*/
        if($_POST["id"])
        {
            // Esta editando.
            $stmt = $db->prepare("UPDATE persona SET nombre = :nombre WHERE id = :id;"); // Preparar la sentencia.
            if(!$stmt) exit($db->lastErrorMsg()); // Morir en el intento si no puede prepararla.
            $stmt->bindValue(":nombre", $_POST["nombre"], SQLITE3_TEXT); // Evitar problemas de inyeccion sql y de sintaxis.
            $stmt->bindValue(":id", $_POST["id"], SQLITE3_INTEGER); // Evitar problemas de inyeccion sql y de sintaxis.
            $stmt->execute();

            // Eliminar telefonos viejos.
            $stmt = $db->prepare("DELETE FROM telefono WHERE id_persona = :id_persona;");
            if(!$stmt) exit($db->lastErrorMsg());            
            $stmt->bindValue(":id_persona", $_POST["id"], SQLITE3_INTEGER);
            $stmt->execute();
            
            // Eliminar emails viejos.
            $stmt = $db->prepare("DELETE FROM email WHERE id_persona = :id_persona;");
            if(!$stmt) exit($db->lastErrorMsg());            
            $stmt->bindValue(":id_persona", $_POST["id"], SQLITE3_INTEGER);
            $stmt->execute();
        }
        else
        {
            // Esta agregando.
            $stmt = $db->prepare("INSERT INTO persona (nombre) VALUES (:nombre);"); // Preparar la sentencia.
            if(!$stmt) exit($db->lastErrorMsg());
            $stmt->bindValue(":nombre", $_POST["nombre"], SQLITE3_TEXT); // Evitar problemas de inyeccion sql y de sintaxis.
            $stmt->execute();
            $_POST["id"] = $db->lastInsertRowID(); // Obtener el id de la persona.
        }
        
        $_POST["tel"] = array_unique(array_filter($_POST["tel"])); // Eliminar telefonos vacios y duplicados.
        if($_POST["tel"])
        {
            foreach($_POST["tel"] as $telefono)
            {
                $stmt = $db->prepare("INSERT INTO telefono (id_persona, telefono) VALUES (:id_persona, :telefono);");
                if(!$stmt) exit($db->lastErrorMsg());

                $stmt->bindValue(":id_persona", $_POST["id"], SQLITE3_INTEGER);
                $stmt->bindValue(":telefono", $telefono, SQLITE3_TEXT);
                $stmt->execute();
            }
        }

        $_POST["email"] = array_unique(array_filter($_POST["email"])); // Eliminar emails vacios y duplicados.
        if($_POST["email"])
        {
            foreach($_POST["email"] as $email)
            {
                $stmt = $db->prepare("INSERT INTO email (id_persona, email) VALUES (:id_persona, :email);");
                if(!$stmt) exit($db->lastErrorMsg());

                $stmt->bindValue(":id_persona", $_POST["id"], SQLITE3_INTEGER);
                $stmt->bindValue(":email", $email, SQLITE3_TEXT);
                $stmt->execute();
            }
        }
    }

    // Cuenta a las personas
    $stmt = $db->prepare("SELECT COUNT(*) AS cantidad FROM persona;");
    if(!$stmt) exit($db->lastErrorMsg());
    $cantidad = $stmt->execute()->fetchArray(SQLITE3_ASSOC)["cantidad"];

    // Traer a todas las personas
    $stmt = $db->prepare("SELECT 
                                persona.id, 
                                persona.nombre, 
                                GROUP_CONCAT(telefonos.telefonos, ', ') telefonos, 
                                GROUP_CONCAT(emails.emails, ', ') emails
                            FROM persona 
                                LEFT JOIN   ( SELECT 
                                                    telefono.id_persona id_persona, 
                                                    GROUP_CONCAT(telefono.telefono, ', ') telefonos 
                                                FROM telefono
                                                 GROUP BY telefono.id_persona
                                            ) telefonos
                                LEFT JOIN   ( SELECT 
                                                    email.id_persona id_persona, 
                                                    GROUP_CONCAT(email.email, ', ') emails 
                                                FROM email
                                                 GROUP BY email.id_persona
                                            ) emails
                                    ON emails.id_persona = persona.id
                            GROUP BY persona.id;");
    if(!$stmt) exit($db->lastErrorMsg());
    $result = $stmt->execute();
?>
    <table class="datatatable">
        <thead>
            <tr>
                <th colspan="5">
                    AGENDA SQLITE
                    <a href="index.php" class="button">Agregar</a>
                    <a href="index.php" class="button active">Listado</a>
                </th>
            </tr>
            <tr>
                <th>
                    #
                </th>
                <th>
                    Nombre
                </th>
                <th>
                    Telefonos
                </th>
                <th>
                    Emails
                </th>
                <th class="actions">
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
        <?php
            if($cantidad)
            {
                while($persona = $result->fetchArray(SQLITE3_ASSOC))
                {
        ?>
            <tr>
                <td>
                    <?=$persona["id"];?>
                </td>
                <td>
                    <?=$persona["nombre"];?>
                </td>
                <td>
                    <?=$persona["telefonos"];?>
                </td>
                <td>
                    <?=$persona["emails"];?>
                </td>
                <td class="actions">
                    <a href="index.php?borrar=<?=$persona["id"];?>" class="button" onclick="return confirm('Esta seguro de eliminar el registro?');"> Borrar </a>
                    <a href="index.php?editar=<?=$persona["id"];?>" class="button">Editar</a>
                </td>
            </tr>

        <?php
                }
            }
            else
            {
        ?>
            <tr>
                <td colspan="5">
                    No se encontraron resultados.
                </td>
            </tr>
        <?php
            }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">
                   <?=$cantidad;?> Personas
                </th>
                <th colspan="2">
                </th>
                <th class="actions">                    
                    <a href="#" class="button"><</a>
                    <a href="#" class="button">></a>
                </th>
            </tr>
        </tfoot>
    </table>
    <form method="post">
        <!-- Campos de control interno -->
        <input type="hidden" name="id" value="<?=(isset($_GET["editar"]) ? $_GET["editar"] : "");?>">
        <input type="hidden" name="save" value="1">

        <!-- Campos publicos -->
        <?php
            $data = [];
            if(isset($_GET["editar"]))
            {
                $stmt = $db->prepare("SELECT nombre FROM persona WHERE id = :id;");
                if(!$stmt) exit($db->lastErrorMsg());

                $stmt->bindValue(":id", $_GET["editar"], SQLITE3_INTEGER);
                $result = $stmt->execute();
                $persona = $result->fetchArray(SQLITE3_ASSOC);

                $stmt = $db->prepare("SELECT telefono FROM telefono WHERE id_persona = :id_persona;");
                if(!$stmt) exit($db->lastErrorMsg());

                $stmt->bindValue(":id_persona", $_GET["editar"], SQLITE3_INTEGER);
                $result = $stmt->execute();

                $persona["telefonos"] = [];
                while($telefono = $result->fetchArray(SQLITE3_ASSOC))
                    $persona["telefonos"][] = $telefono["telefono"];

                $stmt = $db->prepare("SELECT email FROM email WHERE id_persona = :id_persona;");
                if(!$stmt) exit($db->lastErrorMsg());

                $stmt->bindValue(":id_persona", $_GET["editar"], SQLITE3_INTEGER);
                $result = $stmt->execute();

                $persona["emails"] = [];
                while($email = $result->fetchArray(SQLITE3_ASSOC))
                    $persona["emails"][] = $email["email"];
            }
            else
            {
                // Valores por defecto.
                $persona = [];
            }
        ?>
        <fieldset>
            <legend>Persona:</legend>
        <label>Nombre</label>
            <input type="text" name="nombre" required value="<?=(isset($persona["nombre"]) ? $persona["nombre"] : "");?>" placeholder="Ingrese un nombre">
        </fieldset>
        <fieldset>
            <legend>Telefonos: <b onclick="addRow('tel_list')">+Agregar</b></legend>
            <ol id="tel_list">
                <li>
                    <input type="tel" required name="tel[]" value="<?=(isset($persona["telefonos"]) && $persona["telefonos"] ? array_shift($persona["telefonos"]) : "");?>" placeholder="Ingrese un telefono">
                </li>
                <?php
                    // Mostrar los demas telefonos.
                    if(isset($persona["telefonos"]) && $persona["telefonos"])
                    {
                        foreach($persona["telefonos"] as $telefono)
                        {
                ?>
                <li>
                    <input type="tel" name="tel[]" value="<?=$telefono;?>" placeholder="Ingrese un telefono">
                </li>
                <?php
                        }
                    }
                ?>
            </ol>
        </fieldset>
        <fieldset>
            <legend>Emails: <b onclick="addRow('email_list')">+Agregar</b></legend>
            <ol id="email_list">
                <li>
                    <input type="email" required name="email[]" value="<?=(isset($persona["emails"]) && $persona["emails"] ? array_shift($persona["emails"]) : "");?>" placeholder="Ingrese un email">
                </li>
                <?php
                    // Mostrar los demas emails.
                    if(isset($persona["emails"]) && $persona["emails"])
                    {
                        foreach($persona["emails"] as $email)
                        {
                ?>
                <li>
                    <input type="email" name="email[]" value="<?=$email;?>" placeholder="Ingrese un email">
                </li>
                <?php
                        }
                    }
                ?>
            </ol>
        </fieldset>
        <fieldset>
            <legend>Acciones:</legend>
                <input type="submit" value="<?=(isset($_GET["editar"]) ? "Modificar" : "Agregar");?>">
        </fieldset>
    </form>
    </body>
</html>
