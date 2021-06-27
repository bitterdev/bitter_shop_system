- Mollie als Zahlungsanbieter hinzufügen
- 
- README schreiben
- Dokumentation schreiben

Bekannte Bugs:
- Textxattribut wird nicht aus User-Einstellungen übernommen bei Checkout (Vorname + Nachname)
- Copy Products (Detail-Bilder werden nicht kopiert)
- Core-Patch für extend/install.php (um bei den Paket-Details eigene Kategorien anzuzeigen)
- Pagination funktioniert nicht im Frontend
- Nicht die verknüpften Elemente löschen wenn man eine Doctrine Entity löscht
- Calculation issue in total when taxes are included



        <attributekey handle="demo_url" name="Demo URL" package="" searchable="1"
                      indexed="0" type="text" category="product"/>
        <attributekey handle="download_file" name="Download File" package="" searchable="1"
                      indexed="0" type="image_file" category="product"/>