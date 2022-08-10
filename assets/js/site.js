function exportTable(table, filename, sheetName) {

    let tableExport = new TableExport(table, {
        exportButtons: false, 
        filename: filename,
        sheetname: sheetName,
        formats: ["xlsx", "csv", "txt"],
        trimWhitespace: false
    });

    let datos = tableExport.getExportData();
    let preferenciasDocumento = datos[table.id].xlsx;
    tableExport.export2file(preferenciasDocumento.data, preferenciasDocumento.mimeType, preferenciasDocumento.filename, preferenciasDocumento.fileExtension, preferenciasDocumento.merges, preferenciasDocumento.RTL, preferenciasDocumento.sheetname);
}