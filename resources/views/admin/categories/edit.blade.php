@extends('admin.layouts.admin-layout')

@section('title', 'Edit Category')

@section('content')
<h3 class="mb-5">
    Edit category
</h3>
<div class="card" id="edit_cat">
    <div class="card-body">
        @if($category)
        <div>
            <div class="w-100 mb-3 d-flex justify-content-start gap-4">
                <div class="w-50 mb-3">
                    <label for="symbol" class="form-label">Main Name in English (for database only) *</label>
                    <input type="text" class="form-control" id="symbol" v-model="main_name" >
                    <input type="hidden" name="category_id" id="category_id" value="{{ $category->id }}">
                </div>
                <!-- Swiper -->
                <div class="swiper mySwiper w-50 mb-3 pb-5">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" v-for="(language, index) in languages_data" :key="index">
                            <div>
                                <label for="lang_name" class="form-label">Name in @{{language.name}} *</label>
                                <input type="text" class="form-control" id="lang_name" v-model="category_translations[language.symbol]">
                            </div>
                        </div>
                    </div>
                    <div class="swiper-button-next btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M9 6l6 6l-6 6"></path>
                        </svg>
                    </div>
                    <div class="swiper-button-prev btn btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chevron-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M15 6l-6 6l6 6"></path>
                        </svg>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

            <div class="w-100 mb-3 d-flex gap-2">
                <textarea name="description" id="description" cols="30" rows="10" class="form-control w-75" placeholder="Description" v-model="description"></textarea>
                <div class="w-25">
                    <label for="thumbnail" class="w-100 h-100 p-3 d-flex justify-content-center align-items-center form-control" style="max-height: 170px;">
                        <img :src="thumbnail_path ? '/Moheb/dashboard/images/uploads/categories_thumbnail/' + thumbnail_path : '/Moheb/dashboard/images/add_image.svg'" id="preview" alt="img logo" style="width: 100%; max-width: 100%;object-fit: contain;height: 100%;">                                                
                    </label>
                    <input type="file" name="thumbnail" id="thumbnail" class="d-none" @change="photoChanges">
                </div>
            </div>
            <div  class="d-flex justify-content-between gap-4 align-items-end flex-wrap-wrap">
                <div class="w-50" v-if="show_main_categories">
                    <label for="symbol" class="form-label">Choose Main Category *</label>
                    <select name="cat_type" id="cat_type" class="form-control" v-model="main_cat_id">
                        <option v-for="(category, index) in categories_data" :key="index" :value="category.id" v-if="categories_data.length > 0">
                            @{{category.main_name}}
                        </option>
                        <option v-if="!categories_data.length" value="">
                            There is no any Main Category Added
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-50 form-control" style="height: fit-content" @click="save(category_translations, main_name, description, thumbnail)">Save</button>
            </div>
        </div>
        @else
        @php
            return redirect('/Moheb/admin/categories/preview');
        @endphp
        @endif
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('/public/libs/swiper.css') }}">
<style>
    .swiper-button-next, .swiper-button-prev {
        width: fit-content !important;
        height: fit-content !important;
        padding: 4px !important;
        display: flex !important;
        bottom: 0 !important;
        top: auto;
        z-index: 9999;
    }
    .swiper-pagination {
        bottom: 0
    }
    .swiper-button-next::after, .swiper-button-prev::after {
        content: ""
    }
</style>
@endsection

@section('scripts')
<!-- Swiper JS -->
<script src="{{ asset('/public/libs/swiper.js') }}"></script>

<!-- Initialize Swiper -->
<script>
window.onload = function() {
    var swiper = new Swiper(".mySwiper", {
      pagination: {
        el: ".swiper-pagination",
        type: "fraction",
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
};
</script>

<script>
const { createApp, ref } = Vue;

createApp({
  data() {
    return {
      main_name: null,
      main_cat_id: null,
      description: null,
      languages_data: null,
      categories_data: null,
      category_data: null,
      category_names: null,
      category_translations: {},
      show_main_categories: false,
      category_id: undefined,
      thumbnail_path: null,
      thumbnail: null,
    }
  },
  methods: {
    async save(category_translations, main_name, description, thumbnail) {
      $('.loader').fadeIn().css('display', 'flex')
      try {
        const response = await axios.post(`/Moheb/admin/categories/edit`, {
          category_translations: category_translations,
          main_name: main_name,
          description: description,
          category_id: this.category_data.id,
          thumbnail: thumbnail
        },
        {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }
        );
        if (response.data.status === true) {
          document.getElementById('errors').innerHTML = ''
          let error = document.createElement('div')
          error.classList = 'success'
          error.innerHTML = response.data.message
          document.getElementById('errors').append(error)
          $('#errors').fadeIn('slow')
          $('.loader').fadeOut()
          setTimeout(() => {
            $('#errors').fadeOut('slow')
            window.location.href = '/Moheb/admin/categories'
          }, 2000);
        } else {
          $('.loader').fadeOut()
          document.getElementById('errors').innerHTML = ''
          $.each(response.data.errors, function (key, value) {
            let error = document.createElement('div')
            error.classList = 'error'
            error.innerHTML = value
            document.getElementById('errors').append(error)
          });
          $('#errors').fadeIn('slow')
          setTimeout(() => {
            $('input').css('outline', 'none')
            $('#errors').fadeOut('slow')
          }, 5000);
        }

      } catch (error) {
        document.getElementById('errors').innerHTML = ''
        let err = document.createElement('div')
        err.classList = 'error'
        err.innerHTML = 'server error try again later'
        document.getElementById('errors').append(err)
        $('#errors').fadeIn('slow')
        $('.loader').fadeOut()

        setTimeout(() => {
          $('#errors').fadeOut('slow')
        }, 3500);

        console.error(error);
      }
    },
    async getLanguages() {
        $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`/Moheb/admin/categories/get-languages`, {
            },
            );
            if (response.data.status === true) {
                $('.loader').fadeOut()
                this.languages_data = response.data.data
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()
            this.languages_data = false
            setTimeout(() => {
                $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    async getCategories() {
        $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`/Moheb/admin/categories/`, {
            },
            );
            if (response.data.status === true) {
                $('.loader').fadeOut()
                this.categories_data = response.data.data
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()
            this.languages_data = false
            setTimeout(() => {
                $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    async getCategory() {
        $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`/Moheb/admin/category`, {
                category_id: this.category_id
            },
            );
            if (response.data.status === true) {
                $('.loader').fadeOut()
                this.category_data = response.data.data
                this.main_name = this.category_data.main_name
                this.description = this.category_data.description
                this.thumbnail_path = this.category_data.thumbnail_path
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()
            this.languages_data = false
            setTimeout(() => {
                $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    async getNameTranslations() {
        $('.loader').fadeIn().css('display', 'flex')
        try {
            const response = await axios.post(`/Moheb/admin/category/names`, {
                category_id: this.category_id
            },
            );
            if (response.data.status === true) {
                $('.loader').fadeOut()
                this.category_names = response.data.data
                Object.entries(this.category_names).forEach(([key, value]) => {
                    this.category_translations[key] = value
                });
            } else {
                $('.loader').fadeOut()
                document.getElementById('errors').innerHTML = ''
                $.each(response.data.errors, function (key, value) {
                    let error = document.createElement('div')
                    error.classList = 'error'
                    error.innerHTML = value
                    document.getElementById('errors').append(error)
                });
                $('#errors').fadeIn('slow')
                setTimeout(() => {
                    $('input').css('outline', 'none')
                    $('#errors').fadeOut('slow')
                }, 5000);
            }

        } catch (error) {
            document.getElementById('errors').innerHTML = ''
            let err = document.createElement('div')
            err.classList = 'error'
            err.innerHTML = 'server error try again later'
            document.getElementById('errors').append(err)
            $('#errors').fadeIn('slow')
            $('.loader').fadeOut()
            this.languages_data = false
            setTimeout(() => {
                $('#errors').fadeOut('slow')
            }, 3500);

            console.error(error);
        }
    },
    pushCatTranslation(id, name) {
        this.category_translations.push({
            lang_id: id,
            name: name
        })
    },
    chooseCatType(cat_type) {
        if (cat_type == 1) {
            this.getCategories().then(()=> {
                this.show_main_categories = true
            })
        } else {
            this.show_main_categories = false
        }
    },
    photoChanges(event) {
        this.thumbnail = event.target.files[0];
    },
  },
  created() {
    this.getLanguages()
  },
  mounted() {
    this.category_id = document.getElementById('category_id').value ? document.getElementById('category_id').value : undefined;
    this.getCategory()
    this.getNameTranslations()
        $("#thumbnail").change(function () {
        // check if file is valid image
        var file = this.files[0];
        var fileType = file.type;
        var validImageTypes = ["image/gif", "image/jpeg", "image/jpg", "image/png"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            document.getElementById("errors").innerHTML = "";
            let error = document.createElement("div");
            error.classList = "error";
            error.innerHTML =
                "Invalid file type. Please choose a GIF, JPEG, or PNG image.";
            document.getElementById("errors").append(error);
            $("#errors").fadeIn("slow");
            setTimeout(() => {
                $("#errors").fadeOut("slow");
            }, 2000);

            $(this).val(null);
            $("#preview").attr(
                "src",
                this.thumbnail_path ? '/Moheb/dashboard/images/uploads/categories_thumbnail/' + this.thumbnail_path : "/dashboard/images/add_image.svg"
            );
            $(".photo_group i").removeClass("fa-edit").addClass("fa-plus");
        } else {
            // display image preview
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#preview").attr("src", e.target.result);
                $(".photo_group  i")
                    .removeClass("fa-plus")
                    .addClass("fa-edit");
                $(".photo_group label >i").fadeOut("fast");
            };
            reader.readAsDataURL(file);
        }
    });
  },
}).mount('#edit_cat')
</script>
@endsection