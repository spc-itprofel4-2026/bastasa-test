# From marks to code
 
1. Which file is the marking model, and why are the marks not written
   inside the model file?
 
The marking model is app/PIM/equipment_loan.marks.json. The model file
equipment_loan.json says what an equipment loan holds. Table names,
lengths, and unique keys are choices of the platform, so they are marks.
Keeping them outside the model keeps the model platform independent.
 
2. Which mark selected a rule, and which marks provided a value?
   Name the mark and where it goes.
 
unique on equipmentName selected a rule: the unique key rule. tableName on
EquipmentLoan and size on borrowerName and equipmentName provided values.
 
3. Trace table.
 
| Mark      | Target        | Value                  | Where it shows in the generated code              |
|-----------|---------------|------------------------|---------------------------------------------------|
| tableName | EquipmentLoan | school_equipment_loans | migration, createTable line; EquipmentLoanModel   |
| unique    | equipmentName | true                   | migration, addUniqueKey('equipment_name') line    |
| size      | borrowerName  | 60                     | migration, borrower_name constraint 60            |
| size      | equipmentName | 40                     | migration, equipment_name constraint 40           |
 
4. The model file did not change between the first and second marking
   files. What changed in the output?
 
The table name became counter_equipment_loans. The unique key moved from
equipment_name to borrower_name. The lengths changed to 30 and 25. The
columns stayed the same.
 
5. Which marks in Part 3 of your Marking Models Documentation match the
   marks in your marking file? If any are different, say why.
 
This is the instructor test. It has no documentation form, so the marks
come from Part 4 of this guide.
