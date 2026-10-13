# From model to code
 
1. Which file is the model, and why is it platform independent?
The model is app/PIM/equipment_loan.json. It uses only plain types such as text, wholenumber, decimal, yesno, and date. It names no database type and no framework, so it can be used for any platform.
 
2. Which files were generated from it?
The target ci4 wrote app/Database/Migrations/<date>_CreateEquipmentLoanTable.php and app/Models/EquipmentLoanModel.php. The target sql wrote writable/generated/equipment_loans.sql.
 
3. Which mapping rule turned a text field into a column, and what did it become?
In ModelGenerate.php, the rule 'text' => ['VARCHAR', 255] in $ci4Types turned borrowerName into the column borrower_name, type VARCHAR(255). In $sqlTypes, the rule 'text' => 'VARCHAR(100)' (after my change) made the same column VARCHAR(100) in the SQL file.
 
4. What would you change to produce code for a different platform?
I would add a new list of mapping rules for that platform in ModelGenerate.php, for example text becomes String for Java, and add a new target name. The model file equipment_loan.json would stay the same.