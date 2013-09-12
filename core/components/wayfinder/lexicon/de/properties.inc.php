<?php
/**
 * @package wayfinder
 * @subpackage lexicon
 *
 * @language de
 *
 * Wayfinder for MODX Revolution translated to German by Jan-Christoph Ihrens (enigmatic_user, enigma@lunamail.de)
 */
$_lang['prop_wayfinder.level_desc'] = 'Tiefe (Anzahl der Menüebenen), bis zu der das Menü generiert wird. 0 bedeutet, dass alle Menüebenen berücksichtigt werden.';
$_lang['prop_wayfinder.includeDocs_desc'] = 'Fungiert als Filter und begrenzt die Ausgabe auf die Dokumente, die in diesem Parameter angegeben werden. Die startId wird trotzdem benötigt.';
$_lang['prop_wayfinder.excludeDocs_desc'] = 'Fungiert als Filter und entfernt die Dokumente, die in diesem Parameter angegeben werden, aus der Ausgabe. Die startId wird trotzdem benötigt.';
$_lang['prop_wayfinder.contexts_desc'] = 'Gibt die Kontexte für die Ressourcen an, die in diesem Menü berücksichtigt werden. Dies ist nützlich, wenn es mit der startId 0 verwendet wird, um alle Einträge der ersten Ebene anzuzeigen. Hinweis: Hierdurch werden die Ladezeiten ein wenig ansteigen, aber wenn Sie cacheResults auf "Ja" (1) setzen, wird das die Ladezeit ausgleichen.';
$_lang['prop_wayfinder.cacheResults_desc'] = 'Nimmt das generierte Menü in den MODX-Ressourcen-Cache auf. Wenn Sie diese Eigenschaft auf "Ja" (1) setzen, wird das Laden Ihrer Menüs beschleunigt.';
$_lang['prop_wayfinder.cacheTime_desc'] = 'Die Anzahl der Sekunden, die das Menü im Cache gespeichert wird, wenn cacheResults auf "Ja" (1) gesetzt ist. Setzen Sie diese Eigenschaft auf 0, um das Menü dauerhaft im Cache zu speichern, bis dieser manuell geleert wird.';
$_lang['prop_wayfinder.ph_desc'] = 'Um die von Wayfinder generierte Ausgabe an einen Platzhalter zu übergeben, anstatt sie direkt anzuzeigen, setzen Sie den Parameter ph auf den Namen des gewünschten Platzhalters. Die gesamte Ausgabe (inkl. Debugging, falls eingeschaltet) wird zu dem angegebenen Platzhalter geschickt.';
$_lang['prop_wayfinder.debug_desc'] = 'Wenn der Parameter debug auf "Ja" (1) gesetzt ist, gibt Wayfinder Informationen darüber aus, wie jede Ressource verarbeitet wurde.';
$_lang['prop_wayfinder.ignoreHidden_desc'] = 'Der Parameter ignoreHidden erlaubt Wayfinder, die Einstellung "Nicht in Menüs anzeigen" zu ignorieren, die für jedes Dokument gesetzt werden kann. Wenn dieser Parameter auf "Ja" (1) gesetzt wird, werden alle Ressourcen angezeigt, unabhängig von der Einstellung "Nicht in Menüs anzeigen".';
$_lang['prop_wayfinder.hideSubMenus_desc'] = 'Der Parameter hideSubMenus sorgt, wenn er auf "Ja" (1) gesetzt ist, dafür, dass alle nicht aktiven Untermenüs aus der Wayfinder-Ausgabe entfernt werden. Dieser Parameter funktioniert nur, wenn mehrere Menüebenen angezeigt werden.';
$_lang['prop_wayfinder.useWeblinkUrl_desc'] = ' Wenn WebLinks in der Ausgabe vorkommen, gibt Wayfinder denjenigen Link in den Platzhalter [[+wf.link]] aus, der im WebLink-Dokument angegeben ist, anstatt des normalen MODX-Links zum Weblink-Dokument. Um die Standard-Ausgabe von WebLinks zu verwenden (wie jede andere Ressource), setzen Sie diese Eigenschaft auf "Nein" (0).';
$_lang['prop_wayfinder.fullLink_desc'] = 'Wenn diese Eigenschaft auf "Ja" (1) gesetzt ist, wird die gesamte, absolute URL im Link ausgegeben. (Es wird empfohlen, stattdessen die Eigenschaft scheme zu verwenden.)';
$_lang['prop_wayfinder.scheme_desc'] = 'Gibt an, wie die URLs für die Links generiert werden. Wählen Sie "Absolut" ("abs"), um die absolute URL anzuzeigen, "Vollständig" ("full"), um die komplette URL anzuzeigen, und "Relativ" (-1), um die relative URL zu nutzen. Standard ist "Relativ" (wird in der Anzeige ggf. ausgeblendet).<br />
<br />
Bei Übergabe des Parameters scheme beim Aufruf des Wayfinder-Snippets können folgende Werte übergeben werden (basierend auf dem API-Aufruf der Methode makeUrl()):<br />
-1: (Standardwert) URL ist relativ zu site_url<br />
0: siehe http<br />
1: siehe https<br />
full: URL ist absolut und beginnt mit der site_url aus der MODX-Konfiguration<br />
abs: URL ist absolut und beginnt mit der base_url aus der MODX-Konfiguration<br />
http: URL ist absolut; es wird das HTTP-Protokoll erzwungen<br />
https: URL ist absolut; es wird das HTTPS-Protokoll erzwungen';
$_lang['prop_wayfinder.sortOrder_desc'] = 'Erlaubt, das Menü in aufsteigender oder absteigender Ordnung zu sortieren.';
$_lang['prop_wayfinder.sortBy_desc'] = 'Sortiert die Ausgabe nach einem beliebigen Ressourcenfeld (z.B. menuindex oder menutitle) ebenenweise. Das bedeutet, dass jedes Untermenü unabhängig von allen anderen Untermenüs der gleichen Menüebene sortiert wird. Der Wert "random" sorgt dafür, dass die Ausgabe jedes Mal, wenn die Seite geladen wird, anders sortiert wird, wenn das Snippet ungecacht aufgerufen wird.';  // "random" doesn't seem to work!?
$_lang['prop_wayfinder.limit_desc'] = 'Sorgt dafür, dass Wayfinder pro Menüebene nur die hier angegebene Anzahl von Elementen verarbeitet. Der Wert 0 bedeutet, dass alle Elemente verarbeitet werden.';
$_lang['prop_wayfinder.cssTpl_desc'] = 'Dieser Parameter erlaubt die Eingabe des Namens eines Chunks, der einen Link auf eine CSS-Datei oder CSS-Definitionen enthält, die in den Head-Bereich der generierten HTML-Seite eingefügt werden, wenn das Wayfinder-Snippet aufgerufen wird.';
$_lang['prop_wayfinder.jsTpl_desc'] = 'Dieser Parameter erlaubt die Eingabe des Namens eines Chunks, der JavaScript-Code enthält, der in den Head-Bereich der generierten HTML-Seite eingefügt wird, wenn das Wayfinder-Snippet aufgerufen wird.';
$_lang['prop_wayfinder.rowIdPrefix_desc'] = 'Wenn Sie hier etwas anderes als "false" eingeben, füllt Wayfinder den Platzhalter [[+wf.id]] mit einer eindeutigen ID, die aus dem angegebenen Präfix und der Ressourcen-ID besteht.';
$_lang['prop_wayfinder.textOfLinks_desc'] = 'Dieses Feld wird in den Linktext-Platzhalter [[+wf.linktext]] eingefügt.';
$_lang['prop_wayfinder.titleOfLinks_desc'] = 'Dieses Feld wird in den Linktitel-Platzhalter [[+wf.title]] eingefügt.';
$_lang['prop_wayfinder.displayStart_desc'] = 'Das Dokument, das durch den Parameter startId referenziert wird, im Menü mit anzeigen.';
$_lang['prop_wayfinder.firstClass_desc'] = 'CSS-Klasse für das erste Element einer Menüebene.';
$_lang['prop_wayfinder.lastClass_desc'] = 'CSS-Klasse für das letzte Element einer Menüebene.';
$_lang['prop_wayfinder.hereClass_desc'] = 'CSS-Klasse für die Elemente, die anzeigen, wo Sie sich befinden, von der aktuellen Ressource hinauf bis zur obersten Menüebene.';
$_lang['prop_wayfinder.parentClass_desc'] = 'CSS-Klasse für Menüelemente, die Container sind und Kind-Elemente besitzen.';
$_lang['prop_wayfinder.rowClass_desc'] = 'CSS-Klasse für alle Menüeinträge.';
$_lang['prop_wayfinder.outerClass_desc'] = 'CSS-Klasse für das äußere Template (outerTpl).';
$_lang['prop_wayfinder.innerClass_desc'] = 'CSS-Klasse für das innere Template (innerTpl).';
$_lang['prop_wayfinder.levelClass_desc'] = 'CSS-Klasse, die die Menüebene angibt. Die Ebenen-Nummer wird an den angegebenen Klassennamen angehängt (level1, level2, level3 etc., wenn Sie "level" eingegeben haben).';
$_lang['prop_wayfinder.selfClass_desc'] = 'CSS-Klasse für das aktuelle Element.';
$_lang['prop_wayfinder.webLinkClass_desc'] = 'CSS-Klasse für Weblink-Elemente.';
$_lang['prop_wayfinder.outerTpl_desc'] = 'Name des Chunks, der das Template für den äußersten Container enthält; falls dieser Parameter nicht verwendet wird, wird ein String vorausgesetzt, der "&lt;ul&gt;[[+wf.wrapper]]&lt;/ul&gt;" enthält.';
$_lang['prop_wayfinder.rowTpl_desc'] = 'Name des Chunks, der das Template für die regulären Zeilen-Elemente (Menüeinträge) enthält.';
$_lang['prop_wayfinder.parentRowTpl_desc'] = 'Name des Chunks, der das Template für alle Ressourcen enthält, die Container sind und Kind-Elemente besitzen. Vergessen Sie nicht den Platzhalter [[+wf.wrapper]] zur Ausgabe der Kind-Elemente.';
$_lang['prop_wayfinder.parentRowHereTpl_desc'] = 'Name des Chunks, der das Template für die aktuelle Ressource enthält, wenn sie ein Container ist und Kind-Elemente besitzt. Vergessen Sie nicht den Platzhalter [[+wf.wrapper]] zur Ausgabe der Kind-Dokumente.';
$_lang['prop_wayfinder.hereTpl_desc'] = 'Name des Chunks, der das Template für die aktuelle Ressource enthält.';
$_lang['prop_wayfinder.innerTpl_desc'] = 'Name des Chunks, der das Template für alle Untermenüs enthält. Wenn innerTpl nicht angegeben ist, wird stattdessen outerTpl verwendet.';
$_lang['prop_wayfinder.innerRowTpl_desc'] = 'Name des Chunks, der das Template für Zeilen-Elemente (Menüeinträge) in einem Unterordner enthält.';
$_lang['prop_wayfinder.innerHereTpl_desc'] = 'Name des Chunks, der das Template für die aktuelle Ressource enthält, wenn sie sich in einem Unterordner befindet.';
$_lang['prop_wayfinder.activeParentRowTpl_desc'] = 'Name des Chunks, der das Template für Elemente enthält, die Container sind, Kind-Elemente besitzen und momentan im Baum aktiv sind.';
$_lang['prop_wayfinder.categoryFoldersTpl_desc'] = 'Name des Chunks, der das Template für Kategorie-Ordner enthält. Kategorie-Ordner werden dadurch festgelegt, dass das Template der Ressource auf "(leer)" gesetzt wird oder dass in das Ressourcen-Feld link_attributes rel="category" eingetragen wird.';
$_lang['prop_wayfinder.startItemTpl_desc'] = 'Name des Chunks, der das Template für das Start-Element enthält, falls dieses mittels des Parameters displayStart aktiviert wurde. Hinweis: Das Standard-Template zeigt das Start-Element an, verlinkt es aber nicht. Wenn Sie keinen Link benötigen, kann eine Klasse mittels des Parameters &firstClass=`className` zum Standard-Template hinzugefügt werden, anderenfalls müssen Sie ein eigenes Template anlegen.';
$_lang['prop_wayfinder.permissions_desc'] = 'Prüft auf eine Zugriffsberechtigung für die Ressource. Der Standardwert ist "list" - lassen Sie das Feld leer, um die normalen Zugriffsberechtigungs-Überprüfungen zu überspringen.';
$_lang['prop_wayfinder.hereId_desc'] = 'Optional. Wird hier etwas eingetragen, wird die "here"-Ressource in diese ID geändert. Standardmäßig ist das jeweils die momentan aktive Ressource.';
$_lang['prop_wayfinder.where_desc'] = 'Optional. Ein JSON-Objekt mit WHERE-Bedingungen für die Auswahl aller Elemente des Menüs.';
$_lang['prop_wayfinder.templates_desc'] = 'Optional. Eine kommaseparierte Liste von Template-IDs, auf die die Auswahl der Ressourcen beschränkt wird.';
$_lang['prop_wayfinder.previewunpublished_desc'] = 'Optional. Wenn diese Eigenschaft auf "Ja" (1) gesetzt wird, Sie in den Manager eingeloggt sind und das Zugriffsrecht view_unpublished besitzen, werden Sie in die Lage versetzt, eine Vorschau der unveröffentlichten Ressourcen in Ihren Menüs im Front-End anzusehen.';

$_lang['prop_wayfinder.ascending'] = 'Aufsteigend';
$_lang['prop_wayfinder.descending'] = 'Absteigend';
$_lang['prop_wayfinder.absolute'] = 'Absolut';
$_lang['prop_wayfinder.relative'] = 'Relativ';
$_lang['prop_wayfinder.full'] = 'Vollständig';