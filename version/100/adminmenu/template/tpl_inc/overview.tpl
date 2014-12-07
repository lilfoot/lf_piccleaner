{if $notice}
	<br/>
	<div class="userNotice">
		{$notice}
	</div>
	<br />
{/if}
{if $error}
	<br/>
	<div class="userError">
		{$error}
	</div>
	<br />
{/if}

<div id="cleaner_start">
    <center>
        <div>
            <input type="checkbox" name="chkConfirmCleaning" id="chkConfirmCleaning"><label for="chkConfirmCleaning">&nbsp;Ich bin mir bewusst das mit dem Klicken des Buttons Bilder aus meinem Shop gel&ouml;scht werden!</label>
        </div>
        <p>&nbsp;</p>
        <button id="btnStartCleaning" class="button ">Bilderbereinigung starten!</button>
    </center>
</div>

<div id="cleaner_progress" style="display: none;">
    <center>
        <table border="0" style="width: 350px;">
            <tr>
                <td><strong>Gefundene Bilder:</strong></td>
                <td><span id="nPicCount">- wird gerade ermittelt -</span></td>
            </tr>
            <tr>
                <td><strong>Bilder überprüft:</strong></td>
                <td><span id="nPicsChecked">--</span></td>
            </tr>
            <tr>
                <td><strong>Bilder gelöscht:</strong></td>
                <td><span id="nPicsDeleted">--</span></td>
            </tr>
            <tr>
                <td><strong>Gefundene Variations-Bilder:</strong></td>
                <td><span id="nVarPicCount">- in Prüfung -</span></td>
            </tr>
            <tr>
                <td><strong>Variations-Bilder überprüft:</strong></td>
                <td><span id="nVarPicsChecked">--</span></td>
            </tr>
            <tr>
                <td><strong>Variations-Bilder gelöscht:</strong></td>
                <td><span id="nVarPicsDeleted">--</span></td>
            </tr>
        </table>
        <div id="loader">
            <br /><img src="{$oPlugin->cFrontendPfadURL}img/loader.gif" border="0">
        </div>
        <div id="finished" style="display: none;">
            <br /><strong>Der Vorgang wurde beendet!</strong>
        </div>
    </center>
</div>

{literal}
<script>

    $("#btnStartCleaning").on("click", function() {
        if ($("#chkConfirmCleaning").is(":checked")) {
            // Starten
            $("#cleaner_progress").show();
            init();
        }
        else {
            alert("Sie müssen die Checkbox markieren!");
        }
    });

    function init() {
        $("#cleaner_start").hide();

        $.getJSON("../index.php?cleanup=init", function (data)
        {
            $("#nPicCount").html(data.nAnzahl);
            cleanup(0, 0);
        });
    }

    function cleanup(nPointer, nPicsDeleted) {
        $.getJSON("../index.php", {cleanup: "clean", nPointer: nPointer, nPicsDel: nPicsDeleted}, function (data) {
            $("#nPicsChecked").html(data.nPicsChecked);
            $("#nPicsDeleted").html(data.nPicsDeleted);

            if (data.finished == false) {
                // Weiter gehts
                cleanup(data.nPointer, data.nPicsDeleted);
            }
            else {
                // Variations-Bilder als nächstes
                variationen_init();
            }
        });
    }

    function variationen_init() {
        $.getJSON("../index.php?cleanup=variationeninit", function (data)
        {
            $("#nVarPicCount").html(data.nAnzahl);
            varcleanup(0, 0);
        });
    }

    function varcleanup(nPointer, nPicsDeleted) {
        $.getJSON("../index.php", {cleanup: "variationenclean", nPointer: nPointer, nPicsDel: nPicsDeleted}, function (data) {
            $("#nVarPicsChecked").html(data.nPicsChecked);
            $("#nVarPicsDeleted").html(data.nPicsDeleted);

            if (data.finished == false) {
                // Weiter gehts
                varcleanup(data.nPointer, data.nPicsDeleted);
            }
            else {
                // Wir sind fertig
                $("#loader").hide();
                $("#finished").show();
            }
        });
    }

</script>
{/literal}