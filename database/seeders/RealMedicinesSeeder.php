<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RealMedicinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Tablet' => \App\Models\Category::firstOrCreate(['name' => 'Tablet', 'product_type' => 'medicine'], ['slug' => 'tablet']),
            'Syrup' => \App\Models\Category::firstOrCreate(['name' => 'Syrup', 'product_type' => 'medicine'], ['slug' => 'syrup']),
            'Capsule' => \App\Models\Category::firstOrCreate(['name' => 'Capsule', 'product_type' => 'medicine'], ['slug' => 'capsule']),
            'Injection' => \App\Models\Category::firstOrCreate(['name' => 'Injection', 'product_type' => 'medicine'], ['slug' => 'injection']),
            'Drops' => \App\Models\Category::firstOrCreate(['name' => 'Drops', 'product_type' => 'medicine'], ['slug' => 'drops']),
            'Cream' => \App\Models\Category::firstOrCreate(['name' => 'Cream', 'product_type' => 'medicine'], ['slug' => 'cream']),
            'Ointment' => \App\Models\Category::firstOrCreate(['name' => 'Ointment', 'product_type' => 'medicine'], ['slug' => 'ointment']),
        ];

        $medicines = [
            // Painkillers & Fever
            ['name' => 'Panadol 500mg Tablet', 'generic' => 'Paracetamol', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Panadol', 'mfg' => 'GSK'],
            ['name' => 'Panadol Extra Tablet', 'generic' => 'Paracetamol + Caffeine', 'strength' => '500mg/65mg', 'form' => 'Tablet', 'brand' => 'Panadol', 'mfg' => 'GSK'],
            ['name' => 'Panadol CF Tablet', 'generic' => 'Paracetamol + Pseudoephedrine', 'strength' => '500mg/30mg', 'form' => 'Tablet', 'brand' => 'Panadol', 'mfg' => 'GSK'],
            ['name' => 'Calpol Syrup', 'generic' => 'Paracetamol', 'strength' => '120mg/5ml', 'form' => 'Syrup', 'brand' => 'Calpol', 'mfg' => 'GSK'],
            ['name' => 'Brufen 400mg Tablet', 'generic' => 'Ibuprofen', 'strength' => '400mg', 'form' => 'Tablet', 'brand' => 'Brufen', 'mfg' => 'Abbott'],
            ['name' => 'Brufen Syrup', 'generic' => 'Ibuprofen', 'strength' => '100mg/5ml', 'form' => 'Syrup', 'brand' => 'Brufen', 'mfg' => 'Abbott'],
            ['name' => 'Ponstan Forte Tablet', 'generic' => 'Mefenamic Acid', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Ponstan', 'mfg' => 'Pfizer'],
            ['name' => 'Disprin Tablet', 'generic' => 'Aspirin', 'strength' => '300mg', 'form' => 'Tablet', 'brand' => 'Disprin', 'mfg' => 'Reckitt Benckiser'],
            ['name' => 'Synflex Tablet', 'generic' => 'Naproxen', 'strength' => '550mg', 'form' => 'Tablet', 'brand' => 'Synflex', 'mfg' => 'Martin Dow'],
            ['name' => 'Dicloran Tablet', 'generic' => 'Diclofenac Potassium', 'strength' => '50mg', 'form' => 'Tablet', 'brand' => 'Dicloran', 'mfg' => 'Sami'],
            ['name' => 'Voltral 50mg Tablet', 'generic' => 'Diclofenac Sodium', 'strength' => '50mg', 'form' => 'Tablet', 'brand' => 'Voltral', 'mfg' => 'Novartis'],
            
            // Antibiotics
            ['name' => 'Augmentin 625mg Tablet', 'generic' => 'Amoxicillin + Clavulanate', 'strength' => '625mg', 'form' => 'Tablet', 'brand' => 'Augmentin', 'mfg' => 'GSK'],
            ['name' => 'Augmentin 1g Tablet', 'generic' => 'Amoxicillin + Clavulanate', 'strength' => '1g', 'form' => 'Tablet', 'brand' => 'Augmentin', 'mfg' => 'GSK'],
            ['name' => 'Augmentin Syrup 156.25mg', 'generic' => 'Amoxicillin + Clavulanate', 'strength' => '156.25mg/5ml', 'form' => 'Syrup', 'brand' => 'Augmentin', 'mfg' => 'GSK'],
            ['name' => 'Amoxil 250mg Capsule', 'generic' => 'Amoxicillin', 'strength' => '250mg', 'form' => 'Capsule', 'brand' => 'Amoxil', 'mfg' => 'GSK'],
            ['name' => 'Amoxil 500mg Capsule', 'generic' => 'Amoxicillin', 'strength' => '500mg', 'form' => 'Capsule', 'brand' => 'Amoxil', 'mfg' => 'GSK'],
            ['name' => 'Ciproxin 500mg Tablet', 'generic' => 'Ciprofloxacin', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Ciproxin', 'mfg' => 'Bayer'],
            ['name' => 'Novidat 250mg Tablet', 'generic' => 'Ciprofloxacin', 'strength' => '250mg', 'form' => 'Tablet', 'brand' => 'Novidat', 'mfg' => 'Sami'],
            ['name' => 'Novidat 500mg Tablet', 'generic' => 'Ciprofloxacin', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Novidat', 'mfg' => 'Sami'],
            ['name' => 'Leflox 500mg Tablet', 'generic' => 'Levofloxacin', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Leflox', 'mfg' => 'Getz Pharma'],
            ['name' => 'Leflox 250mg Tablet', 'generic' => 'Levofloxacin', 'strength' => '250mg', 'form' => 'Tablet', 'brand' => 'Leflox', 'mfg' => 'Getz Pharma'],
            ['name' => 'Azomax 500mg Capsule', 'generic' => 'Azithromycin', 'strength' => '500mg', 'form' => 'Capsule', 'brand' => 'Azomax', 'mfg' => 'Platinum'],
            ['name' => 'Azomax 250mg Capsule', 'generic' => 'Azithromycin', 'strength' => '250mg', 'form' => 'Capsule', 'brand' => 'Azomax', 'mfg' => 'Platinum'],
            ['name' => 'Velosef 250mg Capsule', 'generic' => 'Cephradine', 'strength' => '250mg', 'form' => 'Capsule', 'brand' => 'Velosef', 'mfg' => 'GSK'],
            ['name' => 'Velosef 500mg Capsule', 'generic' => 'Cephradine', 'strength' => '500mg', 'form' => 'Capsule', 'brand' => 'Velosef', 'mfg' => 'GSK'],
            ['name' => 'Cefspan 400mg Capsule', 'generic' => 'Cefixime', 'strength' => '400mg', 'form' => 'Capsule', 'brand' => 'Cefspan', 'mfg' => 'GSK'],
            ['name' => 'Cefspan Syrup', 'generic' => 'Cefixime', 'strength' => '100mg/5ml', 'form' => 'Syrup', 'brand' => 'Cefspan', 'mfg' => 'GSK'],

            // Gastrointestinal
            ['name' => 'Flagyl 400mg Tablet', 'generic' => 'Metronidazole', 'strength' => '400mg', 'form' => 'Tablet', 'brand' => 'Flagyl', 'mfg' => 'Sanofi'],
            ['name' => 'Flagyl Syrup', 'generic' => 'Metronidazole', 'strength' => '200mg/5ml', 'form' => 'Syrup', 'brand' => 'Flagyl', 'mfg' => 'Sanofi'],
            ['name' => 'Risek 20mg Capsule', 'generic' => 'Omeprazole', 'strength' => '20mg', 'form' => 'Capsule', 'brand' => 'Risek', 'mfg' => 'Getz Pharma'],
            ['name' => 'Risek 40mg Capsule', 'generic' => 'Omeprazole', 'strength' => '40mg', 'form' => 'Capsule', 'brand' => 'Risek', 'mfg' => 'Getz Pharma'],
            ['name' => 'Omega 20mg Capsule', 'generic' => 'Omeprazole', 'strength' => '20mg', 'form' => 'Capsule', 'brand' => 'Omega', 'mfg' => 'Ferozsons'],
            ['name' => 'Esso 20mg Capsule', 'generic' => 'Esomeprazole', 'strength' => '20mg', 'form' => 'Capsule', 'brand' => 'Esso', 'mfg' => 'Highnoon'],
            ['name' => 'Esso 40mg Capsule', 'generic' => 'Esomeprazole', 'strength' => '40mg', 'form' => 'Capsule', 'brand' => 'Esso', 'mfg' => 'Highnoon'],
            ['name' => 'Nexum 40mg Capsule', 'generic' => 'Esomeprazole', 'strength' => '40mg', 'form' => 'Capsule', 'brand' => 'Nexum', 'mfg' => 'Sami'],
            ['name' => 'Gravinate Tablet', 'generic' => 'Dimenhydrinate', 'strength' => '50mg', 'form' => 'Tablet', 'brand' => 'Gravinate', 'mfg' => 'Searle'],
            ['name' => 'Gravinate Syrup', 'generic' => 'Dimenhydrinate', 'strength' => '12.5mg/5ml', 'form' => 'Syrup', 'brand' => 'Gravinate', 'mfg' => 'Searle'],
            ['name' => 'Motilium Tablet', 'generic' => 'Domperidone', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Motilium', 'mfg' => 'Janssen'],
            ['name' => 'Gaviscon Syrup', 'generic' => 'Sodium Alginate', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Gaviscon', 'mfg' => 'Reckitt Benckiser'],
            ['name' => 'Mucaine Syrup', 'generic' => 'Oxethazaine', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Mucaine', 'mfg' => 'Pfizer'],
            ['name' => 'Buscopan Tablet', 'generic' => 'Hyoscine', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Buscopan', 'mfg' => 'Sanofi'],

            // Anti-Allergic & Respiratory
            ['name' => 'Rigix Tablet', 'generic' => 'Cetirizine', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Rigix', 'mfg' => 'AGP'],
            ['name' => 'Rigix Syrup', 'generic' => 'Cetirizine', 'strength' => '5mg/5ml', 'form' => 'Syrup', 'brand' => 'Rigix', 'mfg' => 'AGP'],
            ['name' => 'Zyrtec Tablet', 'generic' => 'Cetirizine', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Zyrtec', 'mfg' => 'GSK'],
            ['name' => 'Softin Tablet', 'generic' => 'Loratadine', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Softin', 'mfg' => 'Bayer'],
            ['name' => 'Telfast 120mg Tablet', 'generic' => 'Fexofenadine', 'strength' => '120mg', 'form' => 'Tablet', 'brand' => 'Telfast', 'mfg' => 'Sanofi'],
            ['name' => 'Telfast 180mg Tablet', 'generic' => 'Fexofenadine', 'strength' => '180mg', 'form' => 'Tablet', 'brand' => 'Telfast', 'mfg' => 'Sanofi'],
            ['name' => 'Arinac Tablet', 'generic' => 'Ibuprofen + Pseudoephedrine', 'strength' => '400mg/60mg', 'form' => 'Tablet', 'brand' => 'Arinac', 'mfg' => 'Abbott'],
            ['name' => 'Acefyl Cough Syrup', 'generic' => 'Diphenhydramine', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Acefyl', 'mfg' => 'GSK'],
            ['name' => 'Pulmonol Syrup', 'generic' => 'Chlorpheniramine', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Pulmonol', 'mfg' => 'Highnoon'],
            ['name' => 'Corex Syrup', 'generic' => 'Dextromethorphan', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Corex', 'mfg' => 'Pfizer'],
            ['name' => 'Ventolin Syrup', 'generic' => 'Salbutamol', 'strength' => '2mg/5ml', 'form' => 'Syrup', 'brand' => 'Ventolin', 'mfg' => 'GSK'],
            ['name' => 'Ventolin Inhaler', 'generic' => 'Salbutamol', 'strength' => '100mcg', 'form' => 'Inhaler', 'brand' => 'Ventolin', 'mfg' => 'GSK'],

            // Vitamins & Supplements
            ['name' => 'Surbex Z Tablet', 'generic' => 'Multivitamins + Zinc', 'strength' => 'Tablet', 'form' => 'Tablet', 'brand' => 'Surbex', 'mfg' => 'Abbott'],
            ['name' => 'Centrum Silver', 'generic' => 'Multivitamins', 'strength' => 'Tablet', 'form' => 'Tablet', 'brand' => 'Centrum', 'mfg' => 'Pfizer'],
            ['name' => 'Sangobion Capsule', 'generic' => 'Iron + Vitamins', 'strength' => 'Capsule', 'form' => 'Capsule', 'brand' => 'Sangobion', 'mfg' => 'Martin Dow'],
            ['name' => 'CAC 1000 Plus', 'generic' => 'Calcium + Vitamin C', 'strength' => '1000mg', 'form' => 'Effervescent Tablet', 'brand' => 'CAC 1000', 'mfg' => 'GSK'],
            ['name' => 'Fefol Vit Capsule', 'generic' => 'Iron + Folic Acid', 'strength' => 'Capsule', 'form' => 'Capsule', 'brand' => 'Fefol', 'mfg' => 'GSK'],
            ['name' => 'Evion 400mg Capsule', 'generic' => 'Vitamin E', 'strength' => '400mg', 'form' => 'Capsule', 'brand' => 'Evion', 'mfg' => 'Martin Dow'],
            ['name' => 'Evion 600mg Capsule', 'generic' => 'Vitamin E', 'strength' => '600mg', 'form' => 'Capsule', 'brand' => 'Evion', 'mfg' => 'Martin Dow'],
            ['name' => 'Zentel Tablet', 'generic' => 'Albendazole', 'strength' => '400mg', 'form' => 'Tablet', 'brand' => 'Zentel', 'mfg' => 'GSK'],

            // Cardiovascular & Blood Pressure
            ['name' => 'Concor 5mg Tablet', 'generic' => 'Bisoprolol', 'strength' => '5mg', 'form' => 'Tablet', 'brand' => 'Concor', 'mfg' => 'Merck'],
            ['name' => 'Tenormin 50mg Tablet', 'generic' => 'Atenolol', 'strength' => '50mg', 'form' => 'Tablet', 'brand' => 'Tenormin', 'mfg' => 'ICI'],
            ['name' => 'Inderal 10mg Tablet', 'generic' => 'Propranolol', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Inderal', 'mfg' => 'ICI'],
            ['name' => 'Inderal 40mg Tablet', 'generic' => 'Propranolol', 'strength' => '40mg', 'form' => 'Tablet', 'brand' => 'Inderal', 'mfg' => 'ICI'],
            ['name' => 'Amlodipine 5mg Tablet', 'generic' => 'Amlodipine', 'strength' => '5mg', 'form' => 'Tablet', 'brand' => 'Amlodipine', 'mfg' => 'Generic'],
            ['name' => 'Exforge 5/160mg Tablet', 'generic' => 'Amlodipine + Valsartan', 'strength' => '5/160mg', 'form' => 'Tablet', 'brand' => 'Exforge', 'mfg' => 'Novartis'],
            ['name' => 'Loprin 75mg Tablet', 'generic' => 'Aspirin', 'strength' => '75mg', 'form' => 'Tablet', 'brand' => 'Loprin', 'mfg' => 'Highnoon'],
            ['name' => 'Ascard 75mg Tablet', 'generic' => 'Aspirin', 'strength' => '75mg', 'form' => 'Tablet', 'brand' => 'Ascard', 'mfg' => 'Atco'],
            ['name' => 'Lipget 10mg Tablet', 'generic' => 'Atorvastatin', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Lipget', 'mfg' => 'Getz Pharma'],
            ['name' => 'Lipget 20mg Tablet', 'generic' => 'Atorvastatin', 'strength' => '20mg', 'form' => 'Tablet', 'brand' => 'Lipget', 'mfg' => 'Getz Pharma'],
            ['name' => 'Rovista 10mg Tablet', 'generic' => 'Rosuvastatin', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Rovista', 'mfg' => 'Getz Pharma'],

            // Diabetes
            ['name' => 'Glucophage 500mg Tablet', 'generic' => 'Metformin', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Glucophage', 'mfg' => 'Merck'],
            ['name' => 'Glucophage 850mg Tablet', 'generic' => 'Metformin', 'strength' => '850mg', 'form' => 'Tablet', 'brand' => 'Glucophage', 'mfg' => 'Merck'],
            ['name' => 'Neodipar 500mg Tablet', 'generic' => 'Metformin', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Neodipar', 'mfg' => 'Sanofi'],
            ['name' => 'Amaryl 1mg Tablet', 'generic' => 'Glimepiride', 'strength' => '1mg', 'form' => 'Tablet', 'brand' => 'Amaryl', 'mfg' => 'Sanofi'],
            ['name' => 'Amaryl 2mg Tablet', 'generic' => 'Glimepiride', 'strength' => '2mg', 'form' => 'Tablet', 'brand' => 'Amaryl', 'mfg' => 'Sanofi'],
            ['name' => 'Amaryl 3mg Tablet', 'generic' => 'Glimepiride', 'strength' => '3mg', 'form' => 'Tablet', 'brand' => 'Amaryl', 'mfg' => 'Sanofi'],
            ['name' => 'Getryl 1mg Tablet', 'generic' => 'Glimepiride', 'strength' => '1mg', 'form' => 'Tablet', 'brand' => 'Getryl', 'mfg' => 'Getz Pharma'],
            ['name' => 'Getryl 2mg Tablet', 'generic' => 'Glimepiride', 'strength' => '2mg', 'form' => 'Tablet', 'brand' => 'Getryl', 'mfg' => 'Getz Pharma'],
            ['name' => 'Janumet 50/500mg Tablet', 'generic' => 'Sitagliptin + Metformin', 'strength' => '50/500mg', 'form' => 'Tablet', 'brand' => 'Janumet', 'mfg' => 'MSD'],
            
            // Topical Creams & Ointments
            ['name' => 'Polyfax Eye Ointment', 'generic' => 'Polymyxin + Bacitracin', 'strength' => '4g', 'form' => 'Ointment', 'brand' => 'Polyfax', 'mfg' => 'GSK'],
            ['name' => 'Polyfax Skin Ointment', 'generic' => 'Polymyxin + Bacitracin', 'strength' => '20g', 'form' => 'Ointment', 'brand' => 'Polyfax', 'mfg' => 'GSK'],
            ['name' => 'Dermovate Cream', 'generic' => 'Clobetasol Propionate', 'strength' => '15g', 'form' => 'Cream', 'brand' => 'Dermovate', 'mfg' => 'GSK'],
            ['name' => 'Betnovate Cream', 'generic' => 'Betamethasone', 'strength' => '15g', 'form' => 'Cream', 'brand' => 'Betnovate', 'mfg' => 'GSK'],
            ['name' => 'Fucidin Cream', 'generic' => 'Fusidic Acid', 'strength' => '15g', 'form' => 'Cream', 'brand' => 'Fucidin', 'mfg' => 'Leo Pharma'],
            ['name' => 'Hydrozole Cream', 'generic' => 'Hydrocortisone + Clotrimazole', 'strength' => '15g', 'form' => 'Cream', 'brand' => 'Hydrozole', 'mfg' => 'GSK'],

            // Others
            ['name' => 'Eritine 10mg Tablet', 'generic' => 'Loratadine', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Eritine', 'mfg' => 'Platinum'],
            ['name' => 'Lexotanil 3mg Tablet', 'generic' => 'Bromazepam', 'strength' => '3mg', 'form' => 'Tablet', 'brand' => 'Lexotanil', 'mfg' => 'Martin Dow'],
            ['name' => 'Xanax 0.25mg Tablet', 'generic' => 'Alprazolam', 'strength' => '0.25mg', 'form' => 'Tablet', 'brand' => 'Xanax', 'mfg' => 'Pfizer'],
            ['name' => 'Xanax 0.5mg Tablet', 'generic' => 'Alprazolam', 'strength' => '0.5mg', 'form' => 'Tablet', 'brand' => 'Xanax', 'mfg' => 'Pfizer'],
            ['name' => 'Rivotril 0.5mg Tablet', 'generic' => 'Clonazepam', 'strength' => '0.5mg', 'form' => 'Tablet', 'brand' => 'Rivotril', 'mfg' => 'Martin Dow'],
            ['name' => 'Rivotril 2mg Tablet', 'generic' => 'Clonazepam', 'strength' => '2mg', 'form' => 'Tablet', 'brand' => 'Rivotril', 'mfg' => 'Martin Dow'],
            ['name' => 'Ginkocer Tablet', 'generic' => 'Ginkgo Biloba', 'strength' => '120mg', 'form' => 'Tablet', 'brand' => 'Ginkocer', 'mfg' => 'Hilal'],
            ['name' => 'Neurobion Tablet', 'generic' => 'Vitamin B Complex', 'strength' => 'Tablet', 'form' => 'Tablet', 'brand' => 'Neurobion', 'mfg' => 'Merck'],
            ['name' => 'Danzen DS Tablet', 'generic' => 'Serratiopeptidase', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Danzen', 'mfg' => 'Takeda'],
            ['name' => 'Methycobal 500mcg Tablet', 'generic' => 'Mecobalamin', 'strength' => '500mcg', 'form' => 'Tablet', 'brand' => 'Methycobal', 'mfg' => 'Hilton'],
            ['name' => 'Serbex Z Tablet', 'generic' => 'Multivitamins', 'strength' => 'Tablet', 'form' => 'Tablet', 'brand' => 'Serbex', 'mfg' => 'Abbott'],
            ['name' => 'Hydryllin Syrup', 'generic' => 'Aminophylline + Diphenhydramine', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Hydryllin', 'mfg' => 'Searle'],
            ['name' => 'Myteka 10mg Tablet', 'generic' => 'Montelukast', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Myteka', 'mfg' => 'Hilton'],
            ['name' => 'Montiget 10mg Tablet', 'generic' => 'Montelukast', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Montiget', 'mfg' => 'Getz Pharma'],
            ['name' => 'Surbex T Tablet', 'generic' => 'Multivitamins', 'strength' => 'Tablet', 'form' => 'Tablet', 'brand' => 'Surbex', 'mfg' => 'Abbott'],
            ['name' => 'Stemetil 5mg Tablet', 'generic' => 'Prochlorperazine', 'strength' => '5mg', 'form' => 'Tablet', 'brand' => 'Stemetil', 'mfg' => 'Sanofi'],
            ['name' => 'Avomine 25mg Tablet', 'generic' => 'Promethazine', 'strength' => '25mg', 'form' => 'Tablet', 'brand' => 'Avomine', 'mfg' => 'Sanofi'],
            ['name' => 'Calamine Lotion', 'generic' => 'Calamine', 'strength' => '100ml', 'form' => 'Lotion', 'brand' => 'Calamine', 'mfg' => 'Generic'],
            ['name' => 'Pyodine Solution', 'generic' => 'Povidone Iodine', 'strength' => '100ml', 'form' => 'Solution', 'brand' => 'Pyodine', 'mfg' => 'Brookes'],
            ['name' => 'Sancos Syrup', 'generic' => 'Dextromethorphan', 'strength' => '120ml', 'form' => 'Syrup', 'brand' => 'Sancos', 'mfg' => 'Novartis'],
            ['name' => 'Kestine 10mg Tablet', 'generic' => 'Ebastine', 'strength' => '10mg', 'form' => 'Tablet', 'brand' => 'Kestine', 'mfg' => 'Highnoon'],
            ['name' => 'Erythrocin 250mg Tablet', 'generic' => 'Erythromycin', 'strength' => '250mg', 'form' => 'Tablet', 'brand' => 'Erythrocin', 'mfg' => 'Abbott'],
            ['name' => 'Erythrocin 500mg Tablet', 'generic' => 'Erythromycin', 'strength' => '500mg', 'form' => 'Tablet', 'brand' => 'Erythrocin', 'mfg' => 'Abbott'],
            ['name' => 'Dalacin C 300mg Capsule', 'generic' => 'Clindamycin', 'strength' => '300mg', 'form' => 'Capsule', 'brand' => 'Dalacin', 'mfg' => 'Pfizer'],
            ['name' => 'Cleocin 150mg Capsule', 'generic' => 'Clindamycin', 'strength' => '150mg', 'form' => 'Capsule', 'brand' => 'Cleocin', 'mfg' => 'Pfizer'],
        ];

        foreach ($medicines as $med) {
            $cat = $categories[$med['form']] ?? $categories['Tablet'];
            $barcode = rand(100000000000, 999999999999);
            
            $medicine = \App\Models\Medicine::create([
                'product_type' => 'medicine',
                'name' => $med['name'],
                'generic_name' => $med['generic'],
                'strength' => $med['strength'],
                'dosage_form' => $med['form'],
                'dosage_unit' => $med['form'],
                'brand' => $med['brand'],
                'manufacturer' => $med['mfg'],
                'barcode' => (string)$barcode,
                'category_id' => $cat->id,
                'status' => 'active',
                'alert_quantity' => 10,
                'reorder_level' => 10,
                'has_expiry' => true,
                'track_batches' => true,
                'tax_rate' => 0,
            ]);

            \App\Models\MedicinePackaging::create([
                'medicine_id' => $medicine->id,
                'unit_id' => \App\Models\Unit::where('name', $med['form'])->first()->id ?? \App\Models\Unit::first()->id,
                'parent_packaging_id' => null,
                'quantity_in_parent' => 1,
                'conversion_to_base' => 1,
                'display_name' => '1 ' . $med['form'],
                'purchase_price' => rand(50, 500),
                'sale_price' => rand(60, 600),
                'barcode' => (string)$barcode,
            ]);
        }
    }
}
