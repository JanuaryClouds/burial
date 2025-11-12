import { faker } from "@faker-js/faker";
document.addEventListener('DOMContentLoaded', () => {
    const autofillBtn = document.getElementById('autofillBtn');
    const burialForm = document.getElementById('kt_create_account_form');
    const gisForm = document.getElementById('gisForm');
    autofillBtn ? console.log('autofillBtn found') : console.log('autofillBtn not found');
    gisForm || burialForm ? console.log('form found') : console.log('form not found');

    function autofillSelect(form, fieldName) {
        const select = form.querySelector(`select[name="${fieldName}"]`);
        if (select) {
            const options = Array.from(select.options).slice(1);
            const random = Math.floor(Math.random() * options.length);
            select.value = options[random].value;
        }
    }

    function optionalField(value, probability = 0.5) {
        return Math.random() < probability ? value : '';
    }

    // if (!autofillBtn || !burialForm || !gisForm) {
    //     return;
    // }

    if (autofillBtn) {
        autofillBtn.addEventListener('click', function (event) {
            event.preventDefault();
            console.log('autofill');
    
            if (burialForm) {
                burialForm.querySelector('input[name="deceased[first_name]"]').value = faker.person.firstName();
                burialForm.querySelector('input[name="deceased[middle_name]"]').value = optionalField(faker.person.middleName(), 0.8);
                burialForm.querySelector('input[name="deceased[last_name]"]').value = faker.person.lastName();
                burialForm.querySelector('input[name="deceased[suffix]"]').value = faker.person.suffix();
                burialForm.querySelector('input[name="deceased[date_of_birth]"]').value = faker.date.birthdate().toISOString().split('T')[0];
                burialForm.querySelector('input[name="deceased[date_of_death]"]').value = faker.date.recent().toISOString().split('T')[0];
                burialForm.querySelector('input[name="deceased[address]"]').value = faker.location.streetAddress();
                autofillSelect(burialForm, 'deceased[gender]');
                autofillSelect(burialForm, 'deceased[barangay_id]');
                autofillSelect(burialForm, 'deceased[religion_id]');
        
                burialForm.querySelector('input[name="claimant[first_name]"]').value = faker.person.firstName();
                burialForm.querySelector('input[name="claimant[middle_name]"]').value = optionalField(faker.person.middleName(), 0.8);
                burialForm.querySelector('input[name="claimant[last_name]"]').value = faker.person.lastName();
                burialForm.querySelector('input[name="claimant[suffix]"]').value = faker.person.suffix();
                burialForm.querySelector('input[name="claimant[mobile_number]"]').value = '09' + faker.number.int({ min: 100000000, max: 999999999 });
                burialForm.querySelector('input[name="claimant[address]"]').value = faker.location.streetAddress();
                autofillSelect(burialForm, 'claimant[relationship_to_deceased]');
                autofillSelect(burialForm, 'claimant[barangay_id]');
        
                burialForm.querySelector('input[name="funeraria"]').value = faker.company.name();
                burialForm.querySelector('input[name="amount"]').value = faker.finance.amount({ min: 100, max: 10000, dec: 0 });
                burialForm.querySelector('textarea[name="remarks"]').value = optionalField(faker.lorem.sentence());
            }
    
            if (gisForm) {
                gisForm.querySelector('input[name="first_name"]').value = faker.person.firstName();
                gisForm.querySelector('input[name="middle_name"]').value = optionalField(faker.person.middleName(), 0.8);
                gisForm.querySelector('input[name="last_name"]').value = faker.person.lastName();
                gisForm.querySelector('input[name="suffix"]').value = optionalField(faker.person.suffix(), 0.3);
                gisForm.querySelector('input[name="age"]').value = faker.number.int({ min: 1, max: 100 });
                autofillSelect(gisForm, 'sex_id');
                gisForm.querySelector('input[name="date_of_birth"]').value = faker.date.birthdate().toISOString().split('T')[0];
                gisForm.querySelector('input[name="house_no"]').value = faker.number.int({ min: 1, max: 100 });
                gisForm.querySelector('input[name="street"]').value = faker.location.streetAddress();
                autofillSelect(gisForm, 'barangay_id');
                autofillSelect(gisForm, 'district_id');
                autofillSelect(gisForm, 'relationship_id');
                autofillSelect(gisForm, 'civil_id');
                autofillSelect(gisForm, 'religion_id');
                autofillSelect(gisForm, 'nationality_id');
                autofillSelect(gisForm, 'education_id');
                gisForm.querySelector('input[name="skill"]').value = faker.word.verb();
                gisForm.querySelector('input[name="income"]').value = faker.finance.amount({ min: 100, max: 10000, dec: 0 });
                gisForm.querySelector('input[name="philhealth"]').value = faker.number.int({ min: 1000000000, max: 9999999999 });
                gisForm.querySelector('input[name="contact_no"]').value = '09' + faker.number.int({ min: 100000000, max: 999999999 });
                gisForm.querySelector('input[name="ben_first_name"]').value = faker.person.firstName();
                gisForm.querySelector('input[name="ben_middle_name"]').value = optionalField(faker.person.middleName(), 0.8);
                gisForm.querySelector('input[name="ben_last_name"]').value = faker.person.lastName();
                gisForm.querySelector('input[name="ben_suffix"]').value = optionalField(faker.person.suffix(), 0.3);
                autofillSelect(gisForm, 'ben_sex_id');
                gisForm.querySelector('input[name="ben_date_of_birth"]').value = faker.date.birthdate().toISOString().split('T')[0];
                gisForm.querySelector('input[name="ben_date_of_death"]').value = faker.date.recent().toISOString().split('T')[0];
                autofillSelect(gisForm, 'ben_religion_id');
                gisForm.querySelector('input[name="ben_place_of_birth"]').value = faker.location.streetAddress();
                autofillSelect(gisForm, 'ben_barangay_id');

                if (gisForm.querySelector('textarea[name="problem"]')) {
                    gisForm.querySelector('textarea[name="problem"]').value = faker.lorem.sentence();
                }
                if (gisForm.querySelector('textarea[name="assessment"]')) {
                    gisForm.querySelector('textarea[name="assessment"]').value = faker.lorem.sentence();
                }
            }
        })
    }
})