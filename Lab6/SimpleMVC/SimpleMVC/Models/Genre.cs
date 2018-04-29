using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.Linq;
using System.Web;

namespace SimpleMVC.Models
{
    public class Genre
    {
        private int id;
        private string name;
        private ICollection<Song> songs;

        public ICollection<Song> Songs { get => songs; set => songs = value; }
        public string Name { get => name; set => name = value; }
        [Key]
        public int Id { get => id; set => id = value; }
    }
}