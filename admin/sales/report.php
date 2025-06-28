<?php
// Export to multiple formats
if(isset($_POST['generate_report'])){
    $type = $_POST['report_type'];
    $data = []; // Fetch data from DB
    
    switch($_POST['export_format']){
        case 'pdf':
            require 'libs/pdf_generator.php';
            generatePDF($data);
            break;
            
        case 'excel':
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="report.xls"');
            // Excel generation code
            break;
            
        case 'csv':
            header('Content-Type: text/csv');
            // CSV generation code
            break;
    }
}
?>

<!-- Report Criteria Form -->
<form method="post">
    <div class="form-group">
        <label>Report Type</label>
        <select name="report_type" class="form-control">
            <option value="inventory">Inventory Report</option>
            <option value="sales">Sales Report</option>
        </select>
    </div>
    
    <div class="form-group">
        <label>Export Format</label>
        <select name="export_format" class="form-control">
            <option value="pdf">PDF</option>
            <option value="excel">Excel</option>
            <option value="csv">CSV</option>
        </select>
    </div>
    
    <button type="submit" name="generate_report" class="btn btn-primary">
        <i class="fas fa-file-export"></i> Generate Report
    </button>
</form>
