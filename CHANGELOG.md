# Changelog

**Version 1.2.0 // xx.xx.2018**

Neu

* Entwicklerversion mit Integration des developer-Addons
  * Templates und Module werden jetzt in Ordnern in git bereitgestellt
* plugin `documentation` integriert, ersetzt pages/info.php und INFO.md
  * Dokumentation für Anwender und Entwickler
* Neues Unterverzeichnis `releases`
  * Erstellung von Releases (installer, github)
  * neue Klasse `rex_addon_release_parameter`
  * neue Klasse `rex_addon_release`
* boot.php hinzugefügt (Entwicklerversion mit developer-Addon)
* CHANGELOG.md hinzugefügt

Änderungen

* REDAXO-Version auf 5.5 gesetzt
* Update jQuery v2.2.4
* Update fullPage 2.9.6
* Update animate.css 3.6.0
* Einstellungen-Seite (pages/settings.php)
  * Defaultwerte setzen
  * CSRF-Schutz
* Install-Seite angepasst (pages/install.php)
  * erweitert für die Erstellung der Releases
  * CSRF-Schutz
* CSS aus Template ausgelagert in die einzelnen Themes (Dino + Theme-Switcher)
* for.png optimierte Version in allen Themes

Bugfixes

* boot.php, rex_developer_manager::setBasePath nur wenn developer-Addon auch wirklich vorhanden ist

**Version 1.1.0beta // 11.11.2017**

* Erste Veröffentlichung des Addons
