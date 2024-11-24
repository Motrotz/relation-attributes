import { defineConfig } from "astro/config";
import starlight from "@astrojs/starlight";

export default defineConfig({
  integrations: [
    starlight({
      title: "Relation Attributes for Laravel",
      editLink: {
        baseUrl:
          "https://github.com/killmails/relation-attributes/edit/main/docs/",
      },
      social: {
        github: "https://github.com/killmails/relation-attributes",
      },
      sidebar: [
        {
          label: "Start Here",
          items: ["getting-started"],
        },
        { label: "Relations", autogenerate: { directory: "relations" } },
      ],
    }),
  ],
});
