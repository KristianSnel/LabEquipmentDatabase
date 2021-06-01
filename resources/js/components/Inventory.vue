<template>
    <div>
        <!---- ADD LOCATION FORM -->
        <div v-if="add_location">
            <button class="btn btn-primary" v-on:click="add_location=false"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
            <div class="panel panel-default">
                <div class="panel-heading">Add New Location</div>
                <div class="panel-body">
                    <form id="location_form" name="location">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="title" required="">
                            <label for="brand">Brand</label>
                            <input type="text" class="form-control" name="address" required="">
                            <label for="image">Image</label>
                            <input type="file" class="form-control media" name="media"/>
                        </div>
                        <button type="submit" class="btn btn-primary" :class="{disabled: isDisabled}" v-on:click.prevent="addLocation">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <div v-else>
            <!---- LOCATIONS LIST -->
            <div v-if="unselected">
                <button class="btn btn-primary pull-right" v-on:click="add_location = true"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add New</button>
                <ul class="list-group">
                    <button type="button" v-bind:key="location.id" class="list-group-item location-tab text-primary" :class="{disabled: list_disable}" v-for="location in locations" v-on:click="fetchItems(location)"><img v-if="location.metadata.hasOwnProperty('picture')" :src="location.metadata.picture.url">{{ location.title }} - {{ location.metadata.address}}</button>
                </ul>
            </div>
            <div v-else>
                <!---- ADD ITEM FORM -->
                <div v-if="add_item">
                    <button class="btn btn-primary" v-on:click="add_item=false"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
                    <div class="panel panel-default">
                        <div class="panel-heading">Add New Item</div>
                        <div class="panel-body">
                            <form id="item_form">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="title">
                                </div>
                                <div class="form-group">
                                    <label for="count">Count</label>
                                    <input type="number" class="form-control" name="count">
                                </div>
                                <div>
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control media" name="media"/>
                                </div>
                                <button type="submit" class="btn btn-primary" :class="{disabled: isDisabled}" v-on:click.prevent="addItem">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!---- EDIT ITEM FORM -->
                <div v-else-if="edit_item">
                    <button class="btn btn-primary" v-on:click="edit_item=false"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
                    <div class="panel panel-default">
                        <div class="panel-heading">Edit {{ selected_item.title }}</div>
                        <div class="panel-body">
                            <form id="edit_item">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" :value="selected_item.title">
                                </div>
                                <div class="form-group">
                                    <label for="count">Count</label>
                                    <input type="number" class="form-control" name="count" :value="selected_item.count">
                                </div>
                                <button type="submit" class="btn btn-primary" :class="{disabled: isDisabled}" v-on:click.prevent="editItem">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <!---- ITEMS LIST -->
                    <button class="btn btn-primary" v-on:click="unselected=true"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Go back</button>
                    <button class="btn btn-primary pull-right" v-on:click="add_item = true"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add New Item</button>
                    <div class="panel panel-default">
                        <div class="panel-heading">{{ selected_location.title }}</div>
                        <div class="panel-body">
                            <ul class="list-group">
                                <button type="button" v-bind:key="item.id" class="list-group-item text-primary location-tab" :class="{disabled: isDisabled}" v-for="item in items"><img>{{ item.title }} - {{ item.count }} <div class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true" v-on:click.prevent="openEdit(item)"></span><span class="glyphicon glyphicon-trash" aria-hidden="true" v-on:click.prevent="deleteItem(item)" style="padding: 0 5px;"></span></div></button>
                            </ul>
                        </div>
                    </div>
                    <div v-if="nfc_scanned">
                        <h2>User detected</h2>
                        Active project: ####
                        
                        <div class="panel-body">
                            <ul class="list-group">
                                <button type="button" v-bind:key="item.id" class="list-group-item text-primary location-tab" :class="{disabled: isDisabled}" v-for="item in items"><img>{{ item.title }} - {{ item.count }} <div class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true" v-on:click.prevent="openEdit(item)"></span><span class="glyphicon glyphicon-trash" aria-hidden="true" v-on:click.prevent="deleteItem(item)" style="padding: 0 5px;"></span></div></button>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        mounted() {
            var self = this;
            //If location slug was passed show items for that location
            if(this.message)
            {
                swal(this.message);
            }
            /*if (this.locationSlug)
            {
                this.unselected = false;
                //find location with slug
                var item = this.locations.filter(function (obj)
                {
                    return obj.slug === self.locationSlug;
                });
                this.selected_location = item[0];
                this.fetchItems(this.selected_location);
            }*/
            this.fetchItems(this.selected_location);
            setTimeout(()=>{
                this.nfc_scanned = true;
            }, 1000)
        },
        props: ['message'],
        data: function () {
            return {
                edit_item: false,
                locations: [{
                    _id: '1',
                    title: 'DEMCON Best',
                    slug:"demcon",
                    metadata: {
                        "count":1,
                        "address": "Kanaaldijk 29 5683 CR Best",
                        "picture": {"url": "https://demcon.com/wp-content/uploads/2020/12/Contact-Best-Demcon-300x300.jpg"}
                    }
                }],
                isDisabled: false,
                list_disable: false,
                unselected: false,
                nfc_scanned: false,
                items: [],
                item: {
                    _id: '1',
                    name: 'henk.jpg',
                    original_name: '',
                    type: '',
                    count: '',
                    created: '',
                    folder: '',
                    location: '',
                    url: '',
                    imgix_url: ''
                },
                add_location: false,
                selected_location: [{
                    _id: '1',
                    title: 'DEMCON Best',
                    slug:"demcon",
                    metadata: {
                        "count":1,
                        "address": "Kanaaldijk 29 5683 CR Best",
                        "picture": {"url": "https://demcon.com/wp-content/uploads/2020/12/Contact-Best-Demcon-300x300.jpg"}
                    }
                }],
                selected_item: [],
                add_item: false
            };
        },
        methods: {
            fetchItems(location)
            {
                //disable the list and fetch items from laravel
                var self = this;
                this.list_disable = true;
                self.items = this.items;
                self.selected_location = location;
                self.unselected = false;
                self.list_disable = false;
                axios.get('api/inventory').then(response => {
                    var data = response.data;
                    this.items = data;
                    //window.location.href = "./" + self.selected_location.slug;
                });
                
            },
            addLocation()
            {
                //disable button
                this.isDisabled = true;
                var image = '';
                var form = $("#location_form")[0];
                var data = new FormData(form);
                //Check if image is selected then upload image first
                if ($("#location_form .media").val() !== '')
                {
                    //delete X-csrf-token default header as it is not accepted by cosmic api
                    /* COMMENTED OUT COSMIC APIdelete axios.defaults.headers.common["X-CSRF-TOKEN"];
                    axios.post('https://api.cosmicjs.com/v1/' + this.slug + '/media', data).then(function (response)
                    {
                        //set x-csrf-token again
                        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                        //get image name, append to formdata and send form data to laravel to add location
                        image = response.data.media.name;
                        data.set('image', image);
                        axios.post('locations/new', data).then(response => {
                            location.reload(true);
                        });
                    });*/
                } else {
                    /* COMMENTED OUT COSMIC API
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
                    //send form data to laravel without image
                    axios.post('locations/new', data).then(response => {
                        location.reload(true);
                    });
                    */
                }
            },
            //set selected item and open edit item section
            openEdit(item)
            {
                this.selected_item = item;
                this.edit_item = true;
            },
            addItem() {
                var self = this;
                this.isDisabled = true;
                var form = $('#item_form')[0];
                var data = new FormData(form);
                data.append('location', this.selected_location._id);
                //this.items.push({'title': data.get('title'), 'metadata.count'});
                if ($("#item_form .media").val() !== '')
                {
                    axios.post('api/inventory', data).then(function (response)
                    {
                        //get image name, append to formdata and send form data to laravel to add location
                        var image = response.data.media.name;
                        data.set('image', image);
                        axios.post('inventory', data).then(response => {
                            //refresh page BUT pass location_slug, which then makes the app load into the passed location
                            //window.location.href = "./" + self.selected_location.slug;
                            
                        });
                    });
                } else {
                    //add header back after post
                    //send form data to laravel without image
                    axios.post('api/inventory', data).then(response => {
                        data = response.data;
                        this.items = data;
                        this.add_item = false;
                        this.isDisabled = false;
                        //window.location.href = "./" + self.selected_location.slug;
                    });
                }
            },
            editItem()
            {
                //edit item, by sending data to IndexController's editItem() function
                var self = this;
                var form = $("#edit_item")[0];
                var data = new FormData(form);
                this.isDisabled = true;
                data.append('slug', this.selected_item.slug);
                if(this.selected_item.metadata.hasOwnProperty('picture')){
                    data.append('image',this.selected_item.metafields[0].value);
                }
                
                data.append('location_id', this.selected_location._id);
                axios.post('items/edit', data).then(response => {
                    //refresh page BUT pass location_slug, which then makes the app load into the passed location
                    window.location.href = "./" + self.selected_location.slug;
                });
            },
            deleteItem(item)
            {
                var self = this;
                swal({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this item!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Nope, still need it'
                }).then(function(){
                    /* COMMENTED OUT COSMIC API
                    axios.get('item/' + item.slug + '/delete').then(response => {
                    window.location.href = "./" + self.selected_location.slug;
                    
                });
                */
                })
                
            }
        }
    }
</script>