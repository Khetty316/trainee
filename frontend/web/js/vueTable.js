const app = Vue.createApp({
    data() {
        return {
            models: window.models,
            selectedIds: [],
            selectAll: false,
            searchCriteria: {},
            sortKey: '',
            sortOrder: 1,
            currentPage: 1,
            numPerPage: window.numPerPage,
            dateVariable: ''
        };
    },
    created() {
        this.initializeSearchCriteria();
//        this.formatDateAttributes();
    },
    computed: {
        // Add a computed property to calculate the total number of pages
        totalPages() {
            return Math.ceil(this.filteredModels.length / this.numPerPage);
        },
        // Modify filteredModels to return only the models for the current page
        paginatedModels() {
            const startIndex = (this.currentPage - 1) * this.numPerPage;
            const endIndex = startIndex + this.numPerPage;
            return this.filteredModels.slice(startIndex, endIndex);
        },
        sortedModels() {
            const key = this.sortKey;
            const modifier = this.sortOrder === 1 ? 1 : -1;
            return this.models.slice().sort((a, b) => {
                const valueA = a[key];
                const valueB = b[key];
                if (valueA === null && valueB === null) {
                    return 0;
                } else if (valueA === null) {
                    return 1 * modifier;
                } else if (valueB === null) {
                    return -1 * modifier;
                }

                const isDate = (value) => value && typeof value === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value);
                const isNumber = (value) => !isNaN(parseFloat(value)) && isFinite(value);
                if (isDate(valueA) && isDate(valueB)) {
                    return (new Date(valueA) - new Date(valueB)) * modifier;
                } else if (isNumber(valueA) && isNumber(valueB)) {
                    return (parseFloat(valueA) - parseFloat(valueB)) * modifier;
                } else if (typeof valueA === 'string' && typeof valueB === 'string') {
                    return valueA.localeCompare(valueB) * modifier;
                } else {
                    return 0;
                }
            });
        },
        // This function filters and returns a subset of sorted models based on search criteria.
        // It checks if any search criteria property has a non-empty value and applies filtering accordingly.
        // The function ensures that only models matching the specified criteria are included in the result.
        filteredModels() {
            const searchCriteria = this.searchCriteria;
            const sortedModels = this.sortedModels;
            if (Object.keys(searchCriteria).some((key) => searchCriteria[key] !== '')) {
                return sortedModels.filter((model) => {
                    return Object.keys(searchCriteria).every((key) => {
                        const modelValue = model[key] ? model[key].toString().toLowerCase() : '';
                        const searchValue = searchCriteria[key].toString().toLowerCase();
                        return searchValue === '' || modelValue.includes(searchValue);
                    });
                });
            } else {
                return sortedModels;
            }
        }
    },
    methods: {
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        // This automatically change the formats of dates but to make it consistent we just call different formatting
//        formatDateAttributes() {
//            for (const model of this.models) {
//                for (const key in model) {
//                    if (Object.prototype.hasOwnProperty.call(model, key)) {
//                        if (this.isDateTimeAttribute(model[key])) {
//                            model[key] = this.formatDate(model[key]);
//                        }
//                    }
//                }
//            }
//        },
//        isDateTimeAttribute(value) {
//            // Define pattern to identify date-time attributes
//            const dateTimePattern = /\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/;
//            return typeof value === 'string' && dateTimePattern.test(value);
//        },


        // This function initializes the search criteria object by setting all its properties to empty strings 
        // and sets up the initial state for the search criteria object, which will be utilized to filter and 
        // retrieve models that match the specified conditions.
        initializeSearchCriteria() {
            const sampleModel = this.models[0];
            for (const key in sampleModel) {
                if (Object.prototype.hasOwnProperty.call(sampleModel, key)) {
                    this.searchCriteria[key] = '';
                }
            }
        },
        // This function sorts the models in the table based on the specified key. It toggles the sorting 
        // order if the same key is clicked again, and it sorts the models in ascending or descending order. 
        // The function handles cases where the values to compare are null, ensuring proper sorting.
        sortTable(key) {
            if (this.sortKey === key) {
                this.sortOrder *= -1;
            } else {
                this.sortKey = key;
                this.sortOrder = 1;
            }

            this.filteredModels.sort((a, b) => {
                const valueA = a[key];
                const valueB = b[key];
                if (valueA === null && valueB === null) {
                    return 0;
                } else if (valueA === null) {
                    return 1 * this.sortOrder;
                } else if (valueB === null) {
                    return -1 * this.sortOrder;
                }

            });
        },
        // Responsible for displaying a date picker widget for a specific attribute input field.
        // It ensures that the input field is cleared if it's not empty, initializes the date picker,
        // and updates the searchCriteria object with the selected date when a date is chosen in the picker.
        showDatePicker(attribute) {
            const inputField = document.getElementById(attribute + '-datepicker');
//            if (inputField.value.trim() !== '') {
//                inputField.value = '';
//                this.searchCriteria[attribute] = '';
//            }

            const picker = new Pikaday({
                field: inputField,
                format: 'DD/MM/YYYY',
                firstDay: 1,
                onSelect: (date) => {
                    this.dateVariable = moment(date).format('YYYY-MM-DD');
                    this.searchCriteria[attribute] = this.dateVariable;
                    picker.destroy();
                }
            });
            picker.show();
        },
        //  Formats a date string (in the format 'YYYY-MM-DD') to 'DD/MM/YYYY' if the input is a valid date,
        //  or returns original value if the input is not a valid date.
        formatDateDMY(dateString) {
            if (!dateString) {
                return '';
            }
            const date = new Date(dateString);
            if (!isNaN(date.getTime())) {
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();
                return `${day}/${month}/${year}`;
            }
        },
        formatDecimalNum(num) {
            // Check if the number is an integer
            if (Number.isInteger(num)) {
                // Add comma for thousand separator and fix to 2 decimal places
                return num.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                // Parse float, add comma for thousand separator, and fix to 2 decimal places
                return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        },

        // This function toggles the selection status of all items in a list based on a "Select All" checkbox 
        // with selectAll v-model. It can be used to select or deselect all items in the list, updating the selection 
        // state of individual items and maintaining a list of selected item IDs. Some use cases are:
        // 1. Use this function in a web application to implement a "Select All" feature for a list of items, 
        //    such as checkboxes for multiple items in a table.
        // 2. It's handy when you want to provide users with the ability to quickly select or deselect all items 
        //    in a list for batch operations, like deleting or processing multiple items at once.
        selectAllItems() {
            if (this.selectAll) {
                this.filteredModels.forEach((model) => {
                    model.isSelected = true;
                    const modelId = parseInt(model.id);
                    if (!this.selectedIds.includes(modelId)) {
                        this.selectedIds.push(modelId);
                    }
                });
            } else {
                this.filteredModels.forEach((model) => {
                    model.isSelected = false;
                    const modelId = parseInt(model.id);
                    const index = this.selectedIds.indexOf(modelId);
                    if (index !== -1) {
                        this.selectedIds.splice(index, 1);
                    }
                });
            }
        },
        updateSelectedIds(model) {
            const modelId = parseInt(model.id);
            if (model.isSelected) {
                if (!this.selectedIds.includes(modelId)) {
                    this.selectedIds.push(modelId);
                }
            } else {
                const index = this.selectedIds.indexOf(modelId);
                if (index !== -1) {
                    this.selectedIds.splice(index, 1);
                }
            }
        },
        confirmAndInitiateAppraisal() {
            const confirmed = window.confirm("Confirm initiate staff appraisal for the selected staff/s?");
            if (confirmed) {
                this.sendSelectedIds('/appraisal/initiate-staff-appraisal');
            }
        },
        confirmAndDeleteAppraisal() {
            const confirmed = window.confirm("Confirm delete staff appraisal for the selected staff/s?");
            if (confirmed) {
                this.sendSelectedIds('/appraisal/delete-staff-appraisal');
            }
        },
        sendSelectedIds(url) {
            const selectedIds = this.selectedIds;
            const year = window.year;
            const csrfToken = window.csrfToken;
            if (selectedIds.length === 0) {
                alert('No IDs selected.');
                return;
            }

            const data = new FormData();
            data.append('selectedIds', JSON.stringify(selectedIds));
            data.append('mainId', mainId);
            fetch(url, {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            const mainId = data.mainId;
                            const redirectUrl = `/appraisal/index-master?id=${mainId}`;
                            window.location.href = redirectUrl;
                        } else {
                            const redirectUrl = `/appraisal/index`;
                            console.log('Failed to delete staff/s appraisal.');
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                    });
        }
    }
});
app.mount('#app');
