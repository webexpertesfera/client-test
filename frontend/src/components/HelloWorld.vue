<template>
  <v-container>
    <v-row class="text-center">
      <v-col cols="6" class="card-outer">
        <v-card elevation="5" color="#deedf5">
          <v-card-title>Please enter website url you want to scan</v-card-title>
          <v-card-text>
            <form @submit.prevent="submitForm">
              <v-text-field
                v-model="url"
                :error-messages="urlErrors"
                label="Url"
                required
                @input="changeInput"
              ></v-text-field>
              <v-btn
                :loading="loading"
                color="primary"
                class="mr-4"
                @click="submitForm"
              >
                submit
              </v-btn>
            </form>
          </v-card-text>
        </v-card>

        <v-card v-if="records.length" elevation="5" color="#deedf5" class="records-table">
          <v-list>
            <v-subheader>Links found:</v-subheader>
            <v-list-item-group color="primary">
              <v-list-item v-for="(item, i) in records" :key="i">
                <v-list-item-avatar>
                  <v-icon class="grey lighten-1" dark> mdi-link </v-icon>
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title text="text" class="text-link">{{
                    item.url
                  }}</v-list-item-title>

                  <v-list-item-subtitle
                    text="foldersubtitle"
                  ></v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action>
                  <v-btn icon> {{ item.code }} </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
    <v-snackbar
      v-model="snackbar.show"
      :timeout="snackbar.timeout"
      :color="snackbar.color"
    >
      {{ snackbar.text }}
    </v-snackbar>
  </v-container>
</template>

<script>
/* eslint-disable no-useless-escape */

import axios from "axios";

export default {
  name: "HelloWorld",
  data: () => {
    return {
      url: "",
      urlErrors: [],
      loading: false,
      snackbar: {
        show: false,
        timeout: 1000,
        text: "",
        color: "red accent-2",
      },
      records: [],
    };
  },
  methods: {
    isInvalidForm() {
      //Check for validatons
      const url = this.url;
      const urlErrorList = [];
      if (!url) {
        urlErrorList.push("Url is required");
      }
      const urlPattern =
        /[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/;
      const isValidURL = urlPattern.test(url);
      if (!isValidURL) {
        urlErrorList.push("Please enter a valid url");
      }

      this.urlErrors = urlErrorList;
      return urlErrorList.length > 0;
    },
    async submitForm() {
      if (this.isInvalidForm()) {
        return;
      }
      const url = this.url;
      // Start the loader
      this.loading = true;
      try {
        const urlObj = new URL(url);
        // Hitting api from laravel
        const { data } = await axios.post("http://localhost:8000/api/scan", {
          url: urlObj.host,
        });
        this.snackbar = {
          timeout: 1000,
          text: `Found ${data.length} links!`,
          show: true,
          color: "green",
        };
        this.records = data;
      } catch (error) {
        // Catch exceptions
        const message = error.response?.data?.message || error.message;
        this.snackbar = {
          timeout: 2000,
          text: message,
          show: true,
          color: "red",
        };
      } finally {
        // Stop the loader
        this.loading = false;
      }
    },
    changeInput() {
      this.urlErrors = [];
    },
  },
};
</script>
<style>
.card-outer {
  margin: 70px auto;
}
.records-table {
  margin-top: 30px;
}
.text-link {
  text-align: left;
}
</style>
