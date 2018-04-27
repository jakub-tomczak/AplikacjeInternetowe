using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Web;

namespace SimpleMVC.Models
{
    public class Song
    {
        public string Name { get => name; set => name = value; }
        public string Artist { get => artist; set => artist = value; }
        public string Genre { get => genre; set => genre = value; }
        [Key]
        public int Id { get => id; set => id = value; }

        private string name;
        private string artist;
        private string genre;
        private int id;
    }
}