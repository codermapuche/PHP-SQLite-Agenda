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
    </head>
    <body>
<?php
    error_reporting(E_ALL);

    // Ubicacion de la base de datos:
    define("_FILE_DB_", "agenda.db");
    $crear = !file_exists(_FILE_DB_);
    $db = new SQLite3(_FILE_DB_);

    // En caso de que sea una base nueva, esto la crea.
    if($crear)
    {
        $db->exec("CREATE TABLE persona (id INTEGER  NOT NULL PRIMARY KEY AUTOINCREMENT, nombre STRING);");
        $db->exec("CREATE TABLE telefono (id_persona INTEGER, telefono STRING);");
        $db->exec("CREATE TABLE email (id_persona INTEGER, email STRING);");
    }

    // Hay que borrar la persona.
    if(isset($_GET["borrar"]))
    {
        // Eliminar la persona.
        $stmt = $db->prepare("DELETE FROM persona WHERE id = :id;"); // Preparar la sentencia.
        if(!$stmt) exit($db->lastErrorMsg()); // Morir en el intento si no puede prepararla.

        $stmt->bindValue(":id", $_GET["borrar"], SQLITE3_INTEGER); // Evitar problemas de inyeccion sql y de sintaxis.
        $stmt->execute();

        // Eliminar telefonos.
        $stmt = $db->prepare("DELETE FROM telefono WHERE id_persona = :id_persona;");
        if(!$stmt) exit($db->lastErrorMsg());

        $stmt->bindValue(":id_persona", $_GET["borrar"], SQLITE3_INTEGER);
        $stmt->execute();

        // Eliminar emails.
        $stmt = $db->prepare("DELETE FROM email WHERE id_persona = :id_persona;");
        if(!$stmt) exit($db->lastErrorMsg());

        $stmt->bindValue(":id_persona", $_GET["borrar"], SQLITE3_INTEGER);
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

            $stmt->bindValue(":id", $_POST["id"], SQLITE3_INTEGER); // Evitar problemas de inyeccion sql y de sintaxis.
            $stmt->bindValue(":nombre", $_POST["nombre"], SQLITE3_TEXT); // Evitar problemas de inyeccion sql y de sintaxis.
            $stmt->execute();

            // Eliminar telefonos viejos.
            $stmt = $db->prepare("DELETE FROM telefono WHERE id_persona = :id_persona;");
            if(!$stmt) exit($db->lastErrorMsg());

            $stmt->bindValue(":id_persona", $_POST["id"], SQLITE3_INTEGER);
            $stmt->execute();

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

            // Eliminar emails viejos.
            $stmt = $db->prepare("DELETE FROM email WHERE id_persona = :id_persona;");
            if(!$stmt) exit($db->lastErrorMsg());

            $stmt->bindValue(":id_persona", $_POST["id"], SQLITE3_INTEGER);
            $stmt->execute();

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
        else
        {
            // Esta agregando.
            $stmt = $db->prepare("INSERT INTO persona (nombre) VALUES (:nombre);"); // Preparar la sentencia.
            if(!$stmt) exit($db->lastErrorMsg());

            $stmt->bindValue(":nombre", $_POST["nombre"], SQLITE3_TEXT); // Evitar problemas de inyeccion sql y de sintaxis.
            $stmt->execute();
            $_POST["id"] = $db->lastInsertRowID(); // Obtener el id de la persona.

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
    }

    // Traer a todas las personas
    $stmt = $db->prepare("SELECT COUNT(*) AS cantidad FROM persona;");
    if(!$stmt) exit($db->lastErrorMsg());

    $result = $stmt->execute();
    $cantidad = $result->fetchArray(SQLITE3_ASSOC)["cantidad"];

    $stmt = $db->prepare("SELECT id, nombre FROM persona;");
    if(!$stmt) exit($db->lastErrorMsg());
    $result = $stmt->execute();
?>
    <table>
        <thead>
            <tr>
                <th colspan="2">
                    AGENDA
                </th>
                <th>
                    <a href="index.php">Agregar</a>
                </th>
            </tr>
            <tr>
                <th>
                    Codigo
                </th>
                <th>
                    Nombre
                </th>
                <th>
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <?php
            if($cantidad)
            {
                while($persona = $result->fetchArray(SQLITE3_ASSOC))
                {
        ?>
            <tr>
                <th>
                    <?=$persona["id"];?>
                </th>
                <th>
                    <?=$persona["nombre"];?>
                </th>
                <th>
                    <a href="index.php?editar=<?=$persona["id"];?>">Editar</a> / <a href="index.php?borrar=<?=$persona["id"];?>" onclick="return confirm('Esta seguro de eliminar el registro?');"> Borrar </a>
                </th>
            </tr>

        <?php
                }
            }
            else
            {
        ?>
            <tr>
                <td colspan="3">
                    No se encontraron resultados.
                </td>
            </tr>
        <?php
            }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">
                   <?=$cantidad;?> Personas
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
