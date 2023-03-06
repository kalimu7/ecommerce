(() => {
    window.addEventListener("elementor/init", () => {
        // for adding items to widget context menu use this below line
        elementor.hooks.addFilter(
            "elements/widget/contextMenuGroups",
            (groups, view) => {

                // Insert Entrance Animation group as third third group
                groups.splice(2, 0, {
                    name: "entranceAnimationGroup",
                    actions: [
                        {
                            name: "copyEntranceAnimation",
                            title: "Copy Entrance Animation",
                            callback: () => {
                                const exportedSettings = {};

                                [
                                    "aux_animation_name",
                                    "aux_fade_in_custom_x",
                                    "aux_fade_in_custom_y",
                                    "aux_scale_custom",
                                    "aux_rotate_custom_deg",
                                    "aux_rotate_custom_origin",
                                    "aux_animation_duration",
                                    "aux_animation_delay",
                                    "aux_animation_easing",
                                    "aux_animation_count",
                                ].forEach((id) => {
                                    exportedSettings[id] =
                                        view.model.getSetting(id);
                                });

                                localStorage.setItem(
                                    "auxElementorEntranceAnimationSettings",
                                    JSON.stringify(exportedSettings)
                                );
                            },
                        },
                        {
                            name: "pasteEntranceAnimation",
                            title: "Paste Entrance Animation",
                            isEnabled: () =>
                                !!localStorage.getItem(
                                    "auxElementorEntranceAnimationSettings"
                                ),
                            callback: () => {
                                const settings = JSON.parse(
                                    localStorage.getItem(
                                        "auxElementorEntranceAnimationSettings"
                                    )
                                );

                                Object.keys(settings).forEach((setting) => {
                                    view.model.setSetting(
                                        setting,
                                        settings[setting]
                                    );
                                });

                                view.model.renderRemoteServer();
                            },
                        },
                    ],
                });

                return groups;
            }
        );

        // for adding items to section context menu use this below line
        elementor.hooks.addFilter(
            "elements/section/contextMenuGroups",
            (groups, view) => {
                return groups;
            }
        );

        // for adding items to column context menu use this below line
        elementor.hooks.addFilter(
            "elements/column/contextMenuGroups",
            (groups, view) => {
                return groups;
            }
        );
    });
})();
