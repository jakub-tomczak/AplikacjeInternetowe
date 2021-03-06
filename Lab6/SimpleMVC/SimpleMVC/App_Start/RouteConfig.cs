﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using System.Web.Routing;

namespace SimpleMVC
{
    public class RouteConfig
    {
        public static void RegisterRoutes(RouteCollection routes)
        {
            routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

            routes.MapRoute(
                name: "Default",
                url: "{controller}/{action}/{id}",
                defaults: new { controller = "Songs", action = "Index", id = UrlParameter.Optional }
            );

            routes.MapRoute(
                name: "Default_Square",
                url: "{controller}/{action}/{id}",
                defaults: new { controller = "Songs", action = "Square", id = 23 }
            );

            //routes.MapRoute(
            //    name: "Genre",
            //    url: "{controller}/{action}",
            //    defaults: new { controller = "Genre", action = "", id = 23 }
            //);

            routes.MapRoute(
                name: "Square",
                url: "{controller}/{action}/{square}/{id}",
                defaults: new { controller = "Songs", action = "Square", id = UrlParameter.Optional }
                );

        }
    }
}
